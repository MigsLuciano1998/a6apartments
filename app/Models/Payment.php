<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'room_billing_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'remarks',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function roomBilling(): BelongsTo
    {
        return $this->belongsTo(RoomBilling::class);
    }

    public function paymentMethodLabel(): string
    {
        return $this->payment_method === 'online' ? 'Online' : 'Cash';
    }
}
