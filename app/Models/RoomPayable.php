<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomPayable extends Model
{
    protected $fillable = [
        'room_number_id',
        'payables_id',
        'amount',
        'frequency',
        'start_date',
        'end_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_number_id');
    }

    public function payable(): BelongsTo
    {
        return $this->belongsTo(Payable::class, 'payables_id');
    }

    public function roomBillings(): HasMany
    {
        return $this->hasMany(RoomBilling::class, 'room_payables_id');
    }

    public function scopeCurrent($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('end_date')
                ->orWhereDate('end_date', '>', now()->toDateString());
        });
    }

    public function isCurrent(): bool
    {
        return $this->end_date === null
            || $this->end_date->gt(now()->startOfDay());
    }

    public function isRecurring(): bool
    {
        return $this->frequency === 'recurring';
    }

    public function frequencyLabel(): string
    {
        return $this->isRecurring() ? 'Recurring' : 'One time';
    }
}
