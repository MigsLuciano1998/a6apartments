<?php

namespace App\Services;

use App\Models\RoomAssignment;
use App\Models\RoomBilling;
use App\Models\RoomPayable;
use Carbon\Carbon;

class MonthlyBillingService
{
    public function syncForMonth(?Carbon $month = null): void
    {
        $month = $month?->copy() ?? now();
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $dueDate = $monthEnd->toDateString();
        $status = $monthEnd->lt(now()->startOfDay()) ? 'over-due' : 'up-coming';

        $assignments = RoomAssignment::query()
            ->current()
            ->with(['room.roomPayables.payable'])
            ->get();

        foreach ($assignments as $assignment) {
            foreach ($assignment->room?->roomPayables ?? [] as $roomPayable) {
                if (! $this->appliesToMonth($roomPayable, $monthStart, $monthEnd)) {
                    continue;
                }

                if ($this->billingExists($assignment, $roomPayable, $monthStart, $monthEnd)) {
                    continue;
                }

                RoomBilling::query()->create([
                    'room_assignment_id' => $assignment->id,
                    'room_payables_id' => $roomPayable->id,
                    'due_date' => $dueDate,
                    'status' => $status,
                ]);
            }
        }
    }

    private function appliesToMonth(RoomPayable $roomPayable, Carbon $monthStart, Carbon $monthEnd): bool
    {
        if ($roomPayable->start_date && $roomPayable->start_date->gt($monthEnd)) {
            return false;
        }

        if ($roomPayable->end_date && $roomPayable->end_date->lt($monthStart)) {
            return false;
        }

        return true;
    }

    private function billingExists(
        RoomAssignment $assignment,
        RoomPayable $roomPayable,
        Carbon $monthStart,
        Carbon $monthEnd,
    ): bool {
        $query = RoomBilling::query()->where('room_payables_id', $roomPayable->id);

        if ($roomPayable->frequency === 'one_time') {
            return $query->exists();
        }

        return $query
            ->where('room_assignment_id', $assignment->id)
            ->whereBetween('due_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->exists();
    }
}
