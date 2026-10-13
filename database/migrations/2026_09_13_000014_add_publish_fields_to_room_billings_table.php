<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_billings', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('status');
            $table->decimal('previous_reading', 12, 2)->nullable()->after('billed_amount');
            $table->decimal('current_reading', 12, 2)->nullable()->after('previous_reading');
        });

        DB::table('room_billings')->whereNull('published_at')->update([
            'published_at' => DB::raw('created_at'),
        ]);
    }

    public function down(): void
    {
        Schema::table('room_billings', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'previous_reading', 'current_reading']);
        });
    }
};
