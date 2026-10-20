<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'room_name',
        'status',
        'remarks',
    ];

    public function roomPayables(): HasMany
    {
        return $this->hasMany(RoomPayable::class, 'room_number_id');
    }

    public function roomAssignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class, 'room_number_id');
    }

    public function currentAssignment()
    {
        return $this->hasOne(RoomAssignment::class, 'room_number_id')
            ->ofMany(['id' => 'max'], function ($query) {
                $query->where('status', 'active')
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', now()->toDateString());
                    });
            });
    }

    /**
     * @return \Illuminate\Support\Collection<int, RoomPayable>
     */
    public function currentPayables()
    {
        return $this->roomPayables->filter(fn (RoomPayable $payable) => $payable->isCurrent());
    }

    public function amountToBePaid(): float
    {
        return (float) $this->currentPayables()->sum('amount');
    }

    public function statusLabel(): string
    {
        return $this->status === 'active' ? 'Available' : 'Not available';
    }
}
