<?php

namespace Database\Seeders;

use App\Models\Payable;
use Illuminate\Database\Seeder;

class PayableSeeder extends Seeder
{
    public function run(): void
    {
        $payables = [
            'Rent',
            'Electricity',
            'Water',
            'Internet',
            'Other Bills',
            'Other Charges',
        ];

        foreach ($payables as $title) {
            Payable::query()->updateOrCreate(
                ['title' => $title],
                [
                    'status' => 'active',
                    'description' => null,
                ]
            );
        }
    }
}
