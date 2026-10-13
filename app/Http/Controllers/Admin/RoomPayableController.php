<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\Room;
use App\Models\RoomPayable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RoomPayableController extends Controller
{
    public function store(Request $request, Room $room): RedirectResponse
    {
        [$payable, $payload] = $this->validatedPayload($request, $room);

        if ($payload['frequency'] === 'recurring' && $this->recurringPayableCurrentlyAssigned($room, $payable->id)) {
            return back()->withInput()->with('error', 'That recurring payable is already assigned to this room.');
        }

        if ($payable->isMetered() && $this->currentMeteredAssigned($room, $payable->id)) {
            return back()->withInput()->with('error', $payable->title.' is already assigned to this room. Enter the monthly reading on the Billing page.');
        }

        $room->roomPayables()->create($payload);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', $payable->title.' was added to this room.');
    }

    public function update(Request $request, Room $room, RoomPayable $roomPayable): RedirectResponse
    {
        $this->ensureBelongsToRoom($room, $roomPayable);

        [$payable, $payload] = $this->validatedPayload($request, $room, $roomPayable);

        if ($payload['frequency'] === 'recurring' && $this->recurringPayableCurrentlyAssigned($room, $payable->id, $roomPayable->id)) {
            return back()->withInput()->with('error', 'That recurring payable is already assigned to this room.');
        }

        if ($payable->isMetered() && $this->currentMeteredAssigned($room, $payable->id, $roomPayable->id)) {
            return back()->withInput()->with('error', $payable->title.' is already assigned to this room.');
        }

        $roomPayable->update($payload);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Room payable updated successfully.');
    }

    public function end(Room $room, RoomPayable $roomPayable): RedirectResponse
    {
        $this->ensureBelongsToRoom($room, $roomPayable);

        if (! $roomPayable->isCurrent()) {
            return back()->with('error', 'This payable is already ended.');
        }

        $roomPayable->update([
            'end_date' => now()->toDateString(),
        ]);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Payable was ended for this room.');
    }

    public function destroy(Room $room, RoomPayable $roomPayable): RedirectResponse
    {
        $this->ensureBelongsToRoom($room, $roomPayable);

        $roomPayable->delete();

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Payable was removed from this room.');
    }

    /**
     * @return array{0: Payable, 1: array<string, mixed>}
     */
    private function validatedPayload(Request $request, Room $room, ?RoomPayable $existing = null): array
    {
        $payableId = $request->validate([
            'payables_id' => ['required', 'exists:payables,id'],
        ])['payables_id'];

        $payable = Payable::query()->findOrFail($payableId);

        if ($payable->status !== 'active' && (! $existing || (int) $existing->payables_id !== (int) $payable->id)) {
            throw ValidationException::withMessages([
                'payables_id' => 'Select an active payable.',
            ]);
        }

        if ($payable->isMetered()) {
            return [$payable, $this->meteredPayload($request, $room, $payable, $existing)];
        }

        $validated = $request->validate($this->standardRules());
        $validated['previous_reading'] = null;
        $validated['current_reading'] = null;
        $validated['billing_formula'] = null;

        return [$payable, $validated];
    }

    /**
     * @return array<string, mixed>
     */
    private function meteredPayload(Request $request, Room $room, Payable $payable, ?RoomPayable $existing): array
    {
        $validated = $request->validate([
            'payables_id' => ['required', 'exists:payables,id'],
            'billing_formula' => ['required', 'string', 'max:500'],
        ]);

        return [
            'payables_id' => $payable->id,
            'amount' => $existing?->amount ?? 0,
            'previous_reading' => $existing?->previous_reading,
            'current_reading' => $existing?->current_reading,
            'billing_formula' => $validated['billing_formula'],
            'frequency' => 'one_time',
            'start_date' => $existing?->start_date?->toDateString() ?? now()->toDateString(),
            'end_date' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function standardRules(): array
    {
        return [
            'payables_id' => ['required', 'exists:payables,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'frequency' => ['required', Rule::in(['recurring', 'one_time'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    private function currentMeteredAssigned(Room $room, int $payableId, ?int $ignoreId = null): bool
    {
        return $room->roomPayables()
            ->current()
            ->where('payables_id', $payableId)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }

    private function ensureBelongsToRoom(Room $room, RoomPayable $roomPayable): void
    {
        abort_unless($roomPayable->room_number_id === $room->id, 404);
    }

    private function recurringPayableCurrentlyAssigned(Room $room, int $payableId, ?int $ignoreId = null): bool
    {
        return $room->roomPayables()
            ->current()
            ->where('frequency', 'recurring')
            ->where('payables_id', $payableId)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
