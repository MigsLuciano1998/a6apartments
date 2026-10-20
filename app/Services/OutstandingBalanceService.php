<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomBilling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OutstandingBalanceService
{
    /**
     * Unpaid remaining charges that have not been fully paid.
     *
     * @return Collection<int, RoomBilling>
     */
    public function previousBalances(?Carbon $month = null): Collection
    {
        return RoomBilling::query()
            ->published()
            ->with([
                'roomPayable.payable',
                'payments',
                'roomAssignment.user',
                'roomAssignment.room',
            ])
            ->orderBy('due_date')
            ->orderBy('id')
            ->get()
            ->filter(fn (RoomBilling $billing) => $billing->amountDue() > 0)
            ->values();
    }

    public function totalRemaining(Collection $billings): float
    {
        return (float) $billings->sum(fn (RoomBilling $billing) => $billing->amountDue());
    }

    public function previousBalanceForTenant(User $tenant, ?Carbon $month = null): float
    {
        $monthStart = ($month?->copy() ?? now())->startOfMonth();

        return $this->remainingFromBillings(
            $tenant->roomAssignments->flatMap->roomBillings,
            $monthStart,
        );
    }

    public function previousBalanceForRoom(Room $room, ?Carbon $month = null): float
    {
        $monthStart = ($month?->copy() ?? now())->startOfMonth();

        return $this->remainingFromBillings(
            $room->roomAssignments->flatMap->roomBillings,
            $monthStart,
        );
    }

    public function currentRemainingForAssignment($assignment): float
    {
        if (! $assignment) {
            return 0.0;
        }

        $month = now();

        return (float) $assignment->roomBillings
            ->filter(function (RoomBilling $billing) use ($month) {
                return $billing->isPublished() && $billing->due_date?->isSameMonth($month);
            })
            ->sum(fn (RoomBilling $billing) => $billing->amountDue());
    }

    public function previousRemainingForAssignment($assignment): float
    {
        if (! $assignment) {
            return 0.0;
        }

        return $this->remainingFromBillings(
            $assignment->roomBillings,
            now()->startOfMonth(),
        );
    }

    public function currentRemainingForTenant(User $tenant): float
    {
        return (float) $tenant->currentAssignments
            ->sum(fn ($assignment) => $this->currentRemainingForAssignment($assignment));
    }

    private function remainingFromBillings(Collection $billings, Carbon $monthStart): float
    {
        return (float) $billings
            ->filter(function (RoomBilling $billing) use ($monthStart) {
                return $billing->isPublished()
                    && $billing->due_date
                    && $billing->due_date->lt($monthStart)
                    && $billing->amountDue() > 0;
            })
            ->sum(fn (RoomBilling $billing) => $billing->amountDue());
    }
}
