<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\Room;
use App\Models\RoomBilling;
use App\Models\RoomPayable;
use App\Services\BillingFormulaEvaluator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class RoomBillingController extends Controller
{
    public function __construct(private BillingFormulaEvaluator $formulaEvaluator) {}

    public function show(Room $room): View
    {
        $room->load([
            'currentAssignment.user',
            'currentAssignment.roomBillings.payments',
            'currentAssignment.roomBillings.roomPayable.payable',
            'roomPayables.payable',
            'roomPayables.roomBillings',
        ]);
        $month = now();
        $assignment = $room->currentAssignment;
        $roomPayables = $this->currentRoomPayables($room);
        $billings = $this->monthBillings($assignment, $month);
        $published = $billings->isNotEmpty() && $billings->every(fn (RoomBilling $billing) => $billing->isPublished());

        $lines = $roomPayables->map(function (RoomPayable $roomPayable) use ($room, $billings) {
            $payable = $roomPayable->payable;
            $billing = $this->billingForPayable($billings, $payable);
            $previousReading = $this->previousReading($room, $payable);

            return [
                'payable' => $payable,
                'roomPayable' => $roomPayable,
                'billing' => $billing,
                'formula' => $roomPayable->billing_formula ?: $payable?->defaultFormula(),
                'previousReading' => $previousReading,
                'currentReading' => old(
                    'items.'.$payable->id.'.current_reading',
                    $billing?->current_reading,
                ),
                'amount' => old(
                    'items.'.$payable->id.'.amount',
                    $billing ? $billing->amountBilled() : $roomPayable->amount,
                ),
            ];
        });

        return view('admin.rooms.billing', [
            'room' => $room,
            'month' => $month,
            'assignment' => $assignment,
            'lines' => $lines,
            'published' => $published,
        ]);
    }

    public function publish(Request $request, Room $room): RedirectResponse
    {
        $assignment = $room->currentAssignment;

        if (! $assignment) {
            return back()->with('error', 'Assign a tenant to this room before publishing a bill.');
        }

        $room->load(['roomPayables.payable', 'roomPayables.roomBillings']);

        $roomPayables = $this->currentRoomPayables($room);

        if ($roomPayables->isEmpty()) {
            return back()->with('error', 'Add payables to this room before publishing a bill.');
        }

        $month = now();
        $monthEnd = $month->copy()->endOfMonth();
        $status = $monthEnd->lt(now()->startOfDay()) ? 'over-due' : 'up-coming';
        $items = $request->input('items', []);

        foreach ($roomPayables as $roomPayable) {
            $payable = $roomPayable->payable;
            $item = $items[$payable->id] ?? [];
            [$amount, $previousReading, $currentReading] = $this->resolvedLine($roomPayable, $item);

            $billing = RoomBilling::query()
                ->where('room_assignment_id', $assignment->id)
                ->whereBetween('due_date', [
                    $month->copy()->startOfMonth()->toDateString(),
                    $monthEnd->toDateString(),
                ])
                ->where(function ($query) use ($roomPayable, $payable) {
                    $query->where('room_payables_id', $roomPayable->id)
                        ->orWhereHas('roomPayable', fn ($roomPayableQuery) => $roomPayableQuery->where('payables_id', $payable->id));
                })
                ->first();

            $payload = [
                'room_assignment_id' => $assignment->id,
                'room_payables_id' => $roomPayable->id,
                'billed_amount' => $amount,
                'previous_reading' => $previousReading,
                'current_reading' => $currentReading,
                'due_date' => $monthEnd->toDateString(),
                'status' => $billing?->status === 'paid' ? 'paid' : $status,
                'published_at' => now(),
            ];

            if ($billing) {
                $billing->update($payload);
            } else {
                RoomBilling::query()->create($payload);
            }

            $roomPayable->update([
                'amount' => $amount,
                'previous_reading' => $previousReading,
                'current_reading' => $currentReading,
            ]);
        }

        return redirect()
            ->route('admin.rooms.billing', $room)
            ->with('success', 'Bill for '.$month->format('F Y').' was published. The tenant can now see it.');
    }

    private function currentRoomPayables(Room $room)
    {
        $room->loadMissing(['roomPayables.payable', 'roomPayables.roomBillings']);

        return $room->roomPayables
            ->filter(fn (RoomPayable $roomPayable) => $roomPayable->payable)
            ->filter(fn (RoomPayable $roomPayable) => $roomPayable->isCurrent() || $roomPayable->payable->isMetered())
            ->sortByDesc(fn (RoomPayable $roomPayable) => $roomPayable->id)
            ->unique('payables_id')
            ->sortBy(fn (RoomPayable $roomPayable) => $roomPayable->payable->title)
            ->values();
    }

    private function billingForPayable($billings, Payable $payable): ?RoomBilling
    {
        return $billings->first(
            fn (RoomBilling $billing) => (int) $billing->roomPayable?->payables_id === (int) $payable->id
        );
    }

    private function monthBillings($assignment, Carbon $month)
    {
        if (! $assignment) {
            return collect();
        }

        $assignment->loadMissing(['roomBillings.payments', 'roomBillings.roomPayable.payable']);

        return $assignment->roomBillings
            ->filter(fn (RoomBilling $billing) => $billing->due_date?->isSameMonth($month))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{0: float, 1: ?float, 2: ?float}
     */
    private function resolvedLine(RoomPayable $roomPayable, array $item): array
    {
        $roomPayable->loadMissing('payable');

        if ($roomPayable->payable?->isMetered()) {
            $currentReading = isset($item['current_reading']) && $item['current_reading'] !== ''
                ? (float) $item['current_reading']
                : null;

            $payableId = $roomPayable->payables_id;

            if ($currentReading === null || $currentReading < 0) {
                throw ValidationException::withMessages([
                    'items.'.$payableId.'.current_reading' => 'Enter the current reading for '.$roomPayable->payable->title.'.',
                ]);
            }

            $previousReading = $this->previousReading($roomPayable->room ?? $roomPayable->load('room')->room, $roomPayable->payable);

            if ($currentReading < $previousReading) {
                throw ValidationException::withMessages([
                    'items.'.$payableId.'.current_reading' => 'Current reading cannot be lower than the previous reading ('.$previousReading.').',
                ]);
            }

            try {
                $amount = $this->formulaEvaluator->evaluate(
                    (string) ($roomPayable->billing_formula ?: $roomPayable->payable->defaultFormula()),
                    $currentReading,
                    $previousReading,
                );
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'items.'.$payableId.'.current_reading' => $exception->getMessage(),
                ]);
            }

            return [$amount, $previousReading, $currentReading];
        }

        $amount = isset($item['amount']) && $item['amount'] !== '' ? (float) $item['amount'] : null;

        if ($amount === null || $amount < 0) {
                throw ValidationException::withMessages([
                    'items.'.$roomPayable->payables_id.'.amount' => 'Enter an amount for '.$roomPayable->payable?->title.'.',
                ]);
        }

        return [round($amount, 2), null, null];
    }

    private function previousReading(Room $room, Payable $payable): float
    {
        $room->loadMissing(['roomPayables.roomBillings']);

        $related = $room->roomPayables
            ->filter(fn (RoomPayable $roomPayable) => (int) $roomPayable->payables_id === (int) $payable->id);

        $lastPublished = $related
            ->flatMap(fn (RoomPayable $roomPayable) => $roomPayable->roomBillings)
            ->filter(fn (RoomBilling $billing) => $billing->isPublished() && $billing->current_reading !== null)
            ->filter(fn (RoomBilling $billing) => ! $billing->due_date?->isSameMonth(now()))
            ->sortByDesc(fn (RoomBilling $billing) => $billing->due_date?->timestamp ?? 0)
            ->first();

        if ($lastPublished) {
            return (float) $lastPublished->current_reading;
        }

        return (float) ($related->sortByDesc('id')->first()?->current_reading ?? 0);
    }
}
