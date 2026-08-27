<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomBilling extends Model
{
    protected $fillable = [
        'room_assignment_id',
        'room_payables_id',
        'due_date',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function roomAssignment(): BelongsTo
    {
        return $this->belongsTo(RoomAssignment::class);
    }

    public function roomPayable(): BelongsTo
    {
        return $this->belongsTo(RoomPayable::class, 'room_payables_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function amountBilled(): float
    {
        return (float) ($this->roomPayable?->amount ?? 0);
    }

    public function amountPaid(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function amountDue(): float
    {
        return max(0, round($this->amountBilled() - $this->amountPaid(), 2));
    }

    public function isPaid(): bool
    {
        return $this->amountDue() <= 0;
    }

    public function isPartiallyPaid(): bool
    {
        return $this->amountPaid() > 0 && $this->amountDue() > 0;
    }

    /**
     * @return 'paid'|'partial'|'unpaid'
     */
    public function paymentStatus(): string
    {
        if ($this->isPaid()) {
            return 'paid';
        }

        if ($this->isPartiallyPaid()) {
            return 'partial';
        }

        return 'unpaid';
    }

    public function isRent(): bool
    {
        return (bool) $this->roomPayable?->payable?->isRent();
    }

    public function periodLabel(): string
    {
        return $this->due_date?->format('F Y') ?? '—';
    }

    public function isOverdue(): bool
    {
        if ($this->amountDue() <= 0 || ! $this->due_date) {
            return false;
        }

        return $this->due_date->lt(now()->startOfDay());
    }
}
