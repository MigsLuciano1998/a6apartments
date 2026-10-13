<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $payableIds = DB::table('payables')
            ->where(function ($query) {
                $query->where('title', 'like', '%water%')
                    ->orWhere('title', 'like', '%electric%');
            })
            ->pluck('id');

        if ($payableIds->isEmpty()) {
            return;
        }

        $rows = DB::table('room_payables')
            ->whereIn('payables_id', $payableIds)
            ->get(['id', 'start_date', 'end_date']);

        foreach ($rows as $row) {
            $endDate = $row->end_date;

            if ($endDate === null && $row->start_date) {
                $endDate = \Carbon\Carbon::parse($row->start_date)->endOfMonth()->toDateString();
            }

            DB::table('room_payables')
                ->where('id', $row->id)
                ->update([
                    'frequency' => 'one_time',
                    'end_date' => $endDate,
                ]);
        }
    }

    public function down(): void
    {
        //
    }
};
