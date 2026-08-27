<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomAssignment extends Model
{
    protected $fillable = [
        'room_number_id',
        'user_id',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_number_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roomBillings(): HasMany
    {
        return $this->hasMany(RoomBilling::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            });
    }

    public function isCurrent(): bool
    {
        return $this->status === 'active'
            && ($this->end_date === null || $this->end_date->gte(now()->startOfDay()));
    }
}
