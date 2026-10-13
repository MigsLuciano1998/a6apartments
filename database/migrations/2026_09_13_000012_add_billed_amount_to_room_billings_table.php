<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_billings', function (Blueprint $table) {
            $table->decimal('billed_amount', 12, 2)->nullable()->after('room_payables_id');
        });
    }

    public function down(): void
    {
        Schema::table('room_billings', function (Blueprint $table) {
            $table->dropColumn('billed_amount');
        });
    }
};
