<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomBilling;
use App\Models\User;
use App\Support\LiveTable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        $month = now();
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $billings = RoomBilling::query()
            ->published()
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

    public function tenant(Request $request): View
    {
        $tenant = auth()->user();
        $month = now();
        $roomsSearch = LiveTable::search($request, 'rooms_q');
        $previousSearch = LiveTable::search($request, 'previous_q');

        $tenant->load([
            'currentAssignments.room',
            'roomAssignments.room',
            'roomAssignments.roomBillings.payments',
            'roomAssignments.roomBillings.roomPayable.payable',
            'currentAssignments.roomBillings.payments',
            'currentAssignments.roomBillings.roomPayable.payable',
        ]);

        $allBillings = $tenant->roomAssignments
            ->flatMap(fn ($assignment) => $assignment->roomBillings->each(
                fn ($billing) => $billing->setRelation('roomAssignment', $assignment)
            ))
            ->filter(fn ($billing) => $billing->isPublished());
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

        $rooms = $tenant->currentAssignments
            ->map(function ($assignment) use ($month) {
                $roomBillings = $assignment->roomBillings
                    ->filter(fn ($billing) => $billing->isPublished() && $billing->due_date?->isSameMonth($month));

                return [
                    'room' => $assignment->room,
                    'currentDue' => (float) $roomBillings->sum(fn ($billing) => $billing->amountDue()),
                    'billCount' => $roomBillings->count(),
                ];
            })
            ->filter(fn ($row) => $row['room'])
            ->values();

        $roomRows = LiveTable::paginateCollection(
            $rooms->filter(fn ($row) => LiveTable::matches($row['room']->room_number, $roomsSearch)
                || LiveTable::matches($row['room']->room_name, $roomsSearch)
                || LiveTable::matches((string) $row['currentDue'], $roomsSearch)
            )->values(),
            $request,
            'rooms_page',
        );

        $pagedPreviousBillings = LiveTable::paginateCollection(
            $previousBillings->filter(fn ($billing) => LiveTable::matches($billing->periodLabel(), $previousSearch)
                || LiveTable::matches($billing->roomAssignment?->room?->room_number, $previousSearch)
                || LiveTable::matches($billing->roomPayable?->payable?->title, $previousSearch)
            )->values(),
            $request,
            'previous_page',
        );

        if (LiveTable::isAjaxFor($request, 'rooms')) {
            return view('dashboards.tenant-rooms-results', [
                'roomRows' => $roomRows,
                'roomsSearch' => $roomsSearch,
            ]);
        }

        if (LiveTable::isAjaxFor($request, 'previous')) {
            return view('dashboards.tenant-previous-results', [
                'previousBillings' => $pagedPreviousBillings,
                'previousSearch' => $previousSearch,
            ]);
        }

        return view('dashboards.tenant', [
            'tenant' => $tenant,
            'month' => $month,
            'rooms' => $rooms,
            'roomRows' => $roomRows,
            'roomsSearch' => $roomsSearch,
            'previousBillings' => $pagedPreviousBillings,
            'previousSearch' => $previousSearch,
            'currentDue' => $currentDue,
            'previousDue' => $previousDue,
            'overdueDue' => $overdueDue,
        ]);
    }
}
