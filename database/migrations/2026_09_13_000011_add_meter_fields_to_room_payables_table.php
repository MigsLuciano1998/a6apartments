<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_payables', function (Blueprint $table) {
            $table->decimal('previous_reading', 12, 2)->nullable()->after('amount');
            $table->decimal('current_reading', 12, 2)->nullable()->after('previous_reading');
            $table->text('billing_formula')->nullable()->after('current_reading');
        });
    }

    public function down(): void
    {
        Schema::table('room_payables', function (Blueprint $table) {
            $table->dropColumn(['previous_reading', 'current_reading', 'billing_formula']);
        });
    }
};
