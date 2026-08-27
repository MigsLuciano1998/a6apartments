<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'title',
        'status',
    ];

    public function roomExpenses(): HasMany
    {
        return $this->hasMany(RoomExpense::class);
    }
}
