<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'JC',
                'middle_name' => null,
                'last_name' => 'Becya',
                'suffix' => null,
                'email' => 'jcbecya@email.com',
                'phone' => '09123456789',
                'role' => 'manager',
                'tenant_type' => null,
                'status' => 'active',
                'password' => 'password',
            ],
            [
                'first_name' => 'Admin',
                'middle_name' => null,
                'last_name' => 'One',
                'suffix' => null,
                'email' => 'admin@email.com',
                'phone' => '09123456780',
                'role' => 'admin',
                'tenant_type' => null,
                'status' => 'active',
                'password' => 'password',
            ],
            [
                'first_name' => 'Tenant',
                'middle_name' => null,
                'last_name' => 'Regular',
                'suffix' => null,
                'email' => 'tenant1@email.com',
                'phone' => '09123456780',
                'role' => 'tenant',
                'tenant_type' => 'regular',
                'status' => 'active',
                'password' => 'password',
            ],
            [
                'first_name' => 'Tenant',
                'middle_name' => null,
                'last_name' => 'Bills',
                'suffix' => null,
                'email' => 'tenant2@email.com',
                'phone' => '09123456780',
                'role' => 'tenant',
                'tenant_type' => 'bills_only',
                'status' => 'active',
                'password' => 'password',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
