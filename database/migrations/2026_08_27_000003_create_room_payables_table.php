<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_payables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_number_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('payables_id')->constrained('payables')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_payables');
    }
};
