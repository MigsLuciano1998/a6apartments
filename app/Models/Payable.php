<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payable extends Model
{
    protected $fillable = [
        'title',
        'status',
        'description',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'payables_id');
    }

    public function roomPayables(): HasMany
    {
        return $this->hasMany(RoomPayable::class, 'payables_id');
    }

    public function isRent(): bool
    {
        return strcasecmp((string) $this->title, 'Rent') === 0;
    }
}
