<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomBilling;
use App\Models\User;
use App\Services\MonthlyBillingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private MonthlyBillingService $monthlyBillingService) {}

    public function admin(): View
    {
        $this->monthlyBillingService->syncForMonth();

        $month = now();
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $billings = RoomBilling::query()
            ->with(['roomPayable.payable', 'payments'])
            ->get();

        $remaining = $billings->filter(fn (RoomBilling $billing) => $billing->amountDue() > 0);
        $currentRemaining = $remaining->filter(fn (RoomBilling $billing) => $billing->due_date?->isSameMonth($month));

        $unpaidRent = (float) $currentRemaining
            ->filter(fn (RoomBilling $billing) => $billing->isRent())
            ->sum(fn (RoomBilling $billing) => $billing->amountDue());
        $unpaidBills = (float) $currentRemaining
            ->reject(fn (RoomBilling $billing) => $billing->isRent())
            ->sum(fn (RoomBilling $billing) => $billing->amountDue());

        $outstanding = (float) $remaining->sum(fn (RoomBilling $billing) => $billing->amountDue());
        $duePayments = $unpaidRent + $unpaidBills;

        $collections = (float) Payment::query()
            ->whereDate('payment_date', '>=', $monthStart->toDateString())
            ->whereDate('payment_date', '<=', $monthEnd->toDateString())
            ->sum('amount');

        $expenses = (float) Expense::query()
            ->whereDate('expense_date', '>=', $monthStart->toDateString())
            ->whereDate('expense_date', '<=', $monthEnd->toDateString())
            ->get()
            ->sum(fn (Expense $expense) => $expense->amountPaid());

        $roleLabel = auth()->user()->isManager() ? 'Manager' : 'Admin';

        return view('dashboards.admin', [
            'roleLabel' => $roleLabel,
            'month' => $month,
            'roomCount' => Room::query()->count(),
            'tenantCount' => User::query()->where('role', 'tenant')->count(),
            'outstanding' => $outstanding,
            'collections' => $collections,
            'expenses' => $expenses,
            'duePayments' => $duePayments,
            'unpaidBills' => $unpaidBills,
            'unpaidRent' => $unpaidRent,
        ]);
    }

    public function tenant(): View
    {
        $tenant = auth()->user();
        $tenant->load('currentAssignment');

        if ($tenant->currentAssignment) {
            $this->monthlyBillingService->syncForMonth();
        }

        $month = now();

        $tenant->load([
            'currentAssignment.room',
            'roomAssignments.room',
            'roomAssignments.roomBillings.payments',
            'roomAssignments.roomBillings.roomPayable.payable',
            'currentAssignment.roomBillings.payments',
            'currentAssignment.roomBillings.roomPayable.payable',
        ]);

        $allBillings = $tenant->roomAssignments->flatMap->roomBillings;
        $currentBillings = $allBillings
            ->filter(fn ($billing) => $billing->due_date?->isSameMonth($month))
            ->sortBy(fn ($billing) => $billing->roomPayable?->payable?->title)
            ->values();
        $previousBillings = $allBillings
            ->filter(fn ($billing) => $billing->due_date && $billing->due_date->lt($month->copy()->startOfMonth()) && $billing->amountDue() > 0)
            ->sortBy('due_date')
            ->values();

        $currentDue = (float) $currentBillings->sum(fn ($billing) => $billing->amountDue());
        $previousDue = (float) $previousBillings->sum(fn ($billing) => $billing->amountDue());
        $overdueDue = (float) $allBillings
            ->filter(fn ($billing) => $billing->isOverdue())
            ->sum(fn ($billing) => $billing->amountDue());

        return view('dashboards.tenant', [
            'tenant' => $tenant,
            'month' => $month,
            'room' => $tenant->currentAssignment?->room,
            'currentBillings' => $currentBillings,
            'previousBillings' => $previousBillings,
            'currentDue' => $currentDue,
            'previousDue' => $previousDue,
            'overdueDue' => $overdueDue,
        ]);
    }
}
