<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payables_id')->constrained('payables')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->text('remarks')->nullable();
            $table->enum('payment_method', ['cash', 'online']);
            $table->enum('status', ['full_paid', 'partial_payment']);
            $table->decimal('partial_payment_amount', 12, 2)->nullable();
            $table->foreignId('cashier')->constrained('users')->restrictOnDelete();
            $table->date('expense_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
