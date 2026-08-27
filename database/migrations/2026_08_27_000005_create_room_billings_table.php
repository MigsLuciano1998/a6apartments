<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_assignment_id')->constrained('room_assignments')->cascadeOnDelete();
            $table->foreignId('room_payables_id')->constrained('room_payables')->cascadeOnDelete();
            $table->date('due_date');
            $table->enum('status', ['up-coming', 'paid', 'over-due'])->default('up-coming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_billings');
    }
};
