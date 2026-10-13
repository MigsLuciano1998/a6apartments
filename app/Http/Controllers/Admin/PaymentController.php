<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\RoomBilling;
use App\Models\User;
use App\Services\OutstandingBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private OutstandingBalanceService $outstandingBalanceService,
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $month = now();
        $tenants = $this->filteredTenants($search);
        $totals = $this->monthTotals($month);

        if ($request->ajax()) {
            return view('admin.payments.results', compact('tenants', 'search', 'month'));
        }

        $payableTenants = User::query()
            ->where('role', 'tenant')
            ->whereHas('currentAssignment')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $paymentsMade = $this->paymentsMadeThisMonth($month);
        $allPreviousBalances = $this->outstandingBalanceService->previousBalances($month)
            ->sortByDesc(fn (RoomBilling $billing) => sprintf(
                '%s-%020d',
                $billing->due_date?->format('Y-m-d') ?? '0000-00-00',
                $billing->id
            ))
            ->values();
        $previousTotal = $this->outstandingBalanceService->totalRemaining($allPreviousBalances);
        $previousBalances = $this->paginatePreviousBalances($request, $allPreviousBalances);

        return view('admin.payments.index', compact(
            'tenants',
            'search',
            'month',
            'totals',
            'payableTenants',
            'paymentsMade',
            'previousBalances',
            'previousTotal',
        ));
    }

    public function tenantPayables(User $tenant): JsonResponse
    {
        abort_unless($tenant->role === 'tenant', 404);

        $assignment = $tenant->currentAssignment;

        if (! $assignment) {
            return response()->json([
                'tenant' => ['id' => $tenant->id, 'name' => $tenant->name],
                'room' => null,
                'payables' => [],
                'message' => 'This tenant is not assigned to a room.',
            ]);
        }

        $assignment->load([
            'room',
            'roomBillings.roomPayable.payable',
            'roomBillings.payments',
        ]);

        $payables = $assignment->roomBillings
            ->filter(fn (RoomBilling $billing) => $this->canRecordPayment($billing))
            ->map(fn (RoomBilling $billing) => [
                'billing_id' => $billing->id,
                'title' => $billing->roomPayable?->payable?->title ?? 'Payable',
                'amount' => $billing->amountDue(),
                'frequency' => $this->paymentLabel($billing),
                'is_rent' => $billing->isRent(),
            ])
            ->values();

        return response()->json([
            'tenant' => ['id' => $tenant->id, 'name' => $tenant->name],
            'room' => $assignment->room?->room_number,
            'payables' => $payables,
            'message' => $payables->isEmpty() ? 'No unpaid payables to record.' : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payPartial = $request->boolean('pay_partial');

        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:users,id'],
            'billing_ids' => ['required', 'array', 'min:1'],
            'billing_ids.*' => ['integer', 'exists:room_billings,id'],
            'pay_partial' => ['sometimes', 'boolean'],
            'partial_amount' => [$payPartial ? 'required' : 'nullable', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(['cash', 'online'])],
            'reference_number' => [
                Rule::requiredIf($request->input('payment_method') === 'online'),
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        if ($payPartial && count($validated['billing_ids']) !== 1) {
            throw ValidationException::withMessages([
                'pay_partial' => 'Partial payment can only be used when one payable is selected.',
            ]);
        }

        $tenant = User::query()->findOrFail($validated['tenant_id']);

        abort_unless($tenant->role === 'tenant', 404);

        $assignment = $tenant->currentAssignment;
        $paidCount = 0;

        DB::transaction(function () use ($validated, $assignment, $payPartial, &$paidCount) {
            $billings = RoomBilling::query()
                ->with(['roomPayable.payable', 'payments'])
                ->whereIn('id', $validated['billing_ids'])
                ->get();

            foreach ($billings as $billing) {
                if ($assignment && $billing->room_assignment_id !== $assignment->id) {
                    continue;
                }

                if (! $this->canRecordPayment($billing)) {
                    continue;
                }

                $amountDue = $billing->amountDue();

                if ($amountDue <= 0) {
                    continue;
                }

                $amount = $amountDue;

                if ($payPartial) {
                    $amount = round((float) $validated['partial_amount'], 2);

                    if ($amount > $amountDue) {
                        throw ValidationException::withMessages([
                            'partial_amount' => 'Amount cannot exceed the remaining balance of '.number_format($amountDue, 2).'.',
                        ]);
                    }
                }

                $billing->payments()->create([
                    'amount' => $amount,
                    'payment_method' => $validated['payment_method'],
                    'payment_date' => now()->toDateString(),
                    'reference_number' => $validated['payment_method'] === 'online'
                        ? $validated['reference_number']
                        : null,
                    'remarks' => $payPartial && $amount < $amountDue
                        ? 'Partial payment from Payments page'
                        : 'Recorded from Payments page',
                ]);

                $billing->load('payments');

                if ($billing->amountDue() <= 0) {
                    $billing->update(['status' => 'paid']);
                }

                $paidCount++;
            }
        });

        if ($paidCount === 0) {
            return redirect()
                ->route('admin.payments.index')
                ->with('error', 'No unpaid payables were selected.');
        }

        return redirect()
            ->route('admin.payments.index')
            ->with('success', $paidCount === 1
                ? 'Payment recorded successfully.'
                : $paidCount.' payments recorded successfully.');
    }

    /**
     * @return array{rent: float, bills: float}
     */
    private function monthTotals($month): array
    {
        $billings = RoomBilling::query()
            ->published()
            ->with(['roomPayable.payable', 'payments'])
            ->whereBetween('due_date', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
            ])
            ->get();

        $unpaid = $billings->filter(fn (RoomBilling $billing) => ! $billing->isPaid());

        return [
            'rent' => (float) $unpaid->filter(fn (RoomBilling $billing) => $billing->isRent())->sum(fn (RoomBilling $billing) => $billing->amountDue()),
            'bills' => (float) $unpaid->reject(fn (RoomBilling $billing) => $billing->isRent())->sum(fn (RoomBilling $billing) => $billing->amountDue()),
        ];
    }

    private function filteredTenants(string $search)
    {
        $month = now();

        return User::query()
            ->where('role', 'tenant')
            ->with([
                'currentAssignment.room',
                'currentAssignment.roomBillings' => fn ($query) => $query->published()->whereBetween('due_date', [
                    $month->copy()->startOfMonth()->toDateString(),
                    $month->copy()->endOfMonth()->toDateString(),
                ]),
                'currentAssignment.roomBillings.roomPayable.payable',
                'currentAssignment.roomBillings.payments',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('suffix', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('currentAssignment.room', function ($query) use ($search) {
                            $query->where('room_number', 'like', "%{$search}%")
                                ->orWhere('room_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(10)
            ->withQueryString();
    }

    private function isCurrentMonth(RoomBilling $billing): bool
    {
        if (! $billing->due_date) {
            return false;
        }

        return $billing->due_date->isSameMonth(now());
    }

    private function canRecordPayment(RoomBilling $billing): bool
    {
        if (! $billing->isPublished() || $billing->isPaid()) {
            return false;
        }

        if ($this->isCurrentMonth($billing)) {
            return true;
        }

        return (bool) $billing->roomPayable?->payable?->isMetered();
    }

    private function paymentLabel(RoomBilling $billing): string
    {
        $frequency = $billing->roomPayable?->frequencyLabel() ?? 'Bill';

        if ($billing->roomPayable?->payable?->isMetered()) {
            return $frequency.' · '.$billing->periodLabel();
        }

        return $frequency.($billing->isRent() ? ' · Rent' : ' · Bill');
    }

    /**
     * @param  \Illuminate\Support\Collection<int, RoomBilling>  $balances
     */
    private function paginatePreviousBalances(Request $request, $balances): LengthAwarePaginator
    {
        $perPage = 10;
        $page = max(1, (int) $request->query('balances_page', 1));

        return (new LengthAwarePaginator(
            $balances->forPage($page, $perPage)->values(),
            $balances->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'balances_page',
                'fragment' => 'previous-balances',
            ]
        ))->withQueryString();
    }

    private function paymentsMadeThisMonth($month)
    {
        return Payment::query()
            ->with([
                'roomBilling.roomPayable.payable',
                'roomBilling.roomAssignment.user',
                'roomBilling.roomAssignment.room',
            ])
            ->whereDate('payment_date', '>=', $month->copy()->startOfMonth()->toDateString())
            ->whereDate('payment_date', '<=', $month->copy()->endOfMonth()->toDateString())
            ->latest('payment_date')
            ->latest('id')
            ->get();
    }
}
