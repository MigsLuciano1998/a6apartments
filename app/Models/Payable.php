<?php

namespace App\Models;

use App\Services\BillingFormulaEvaluator;
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

    public function isWater(): bool
    {
        return str_contains(strtolower((string) $this->title), 'water');
    }

    public function isElectricity(): bool
    {
        return str_contains(strtolower((string) $this->title), 'electric');
    }

    public function isMetered(): bool
    {
        return $this->isWater() || $this->isElectricity();
    }

    public function meterKind(): string
    {
        if ($this->isWater()) {
            return 'water';
        }

        if ($this->isElectricity()) {
            return 'electricity';
        }

        return 'standard';
    }

    public function defaultFormula(): ?string
    {
        if ($this->isWater()) {
            return BillingFormulaEvaluator::WATER_FORMULA;
        }

        if ($this->isElectricity()) {
            return BillingFormulaEvaluator::ELECTRICITY_FORMULA;
        }

        return null;
    }
}
