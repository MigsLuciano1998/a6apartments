<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Support\LiveTable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomBillingController extends Controller
{
    public function show(Request $request, Room $room): View
    {
        $assignment = $room->roomAssignments()
            ->where('user_id', auth()->id())
            ->current()
            ->first();

        abort_unless($assignment, 404);

        $assignment->load([
            'roomBillings.payments',
            'roomBillings.roomPayable.payable',
        ]);

        $month = now();
        $search = LiveTable::search($request);
        $billings = $assignment->roomBillings
            ->filter(fn ($billing) => $billing->isPublished())
            ->filter(function ($billing) use ($month) {
                return $billing->due_date?->isSameMonth($month)
                    || $billing->amountDue() > 0;
            })
            ->sortBy(fn ($billing) => sprintf(
                '%s-%s',
                $billing->due_date?->format('Y-m-d') ?? '0000-00-00',
                $billing->roomPayable?->payable?->title ?? '',
            ))
            ->values();

        $filtered = $billings->filter(function ($billing) use ($search) {
            $isMetered = (bool) $billing->roomPayable?->payable?->isMetered();
            $reading = $isMetered && $billing->current_reading !== null
                ? number_format((float) $billing->current_reading, 2)
                : 'N/A';

            return LiveTable::matches($billing->roomPayable?->payable?->title, $search)
                || LiveTable::matches((string) $billing->amountDue(), $search)
                || LiveTable::matches($reading, $search)
                || LiveTable::matches($billing->due_date?->format('M d, Y'), $search);
        })->values();

        $billings = LiveTable::paginateCollection($filtered, $request);

        if ($request->ajax()) {
            return view('tenant.billing.results', compact('billings', 'search'));
        }

        return view('tenant.billing.show', [
            'room' => $room,
            'assignment' => $assignment,
            'month' => $month,
            'billings' => $billings,
            'search' => $search,
        ]);
    }
}
