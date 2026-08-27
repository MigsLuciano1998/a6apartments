<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'payables_id',
        'amount',
        'remarks',
        'payment_method',
        'status',
        'partial_payment_amount',
        'cashier',
        'expense_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'partial_payment_amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function payable(): BelongsTo
    {
        return $this->belongsTo(Payable::class, 'payables_id');
    }

    public function cashierUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier');
    }

    public function isPartial(): bool
    {
        return $this->status === 'partial_payment';
    }

    public function amountPaid(): float
    {
        if ($this->isPartial()) {
            return (float) ($this->partial_payment_amount ?? 0);
        }

        return (float) $this->amount;
    }

    public function amountRemaining(): float
    {
        return max(0, round((float) $this->amount - $this->amountPaid(), 2));
    }

    public function paymentMethodLabel(): string
    {
        return $this->payment_method === 'online' ? 'Online' : 'Cash';
    }

    public function statusLabel(): string
    {
        return $this->isPartial() ? 'Partial payment' : 'Fully paid';
    }
}
