<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::bind('tenant', function (string $value) {
            return User::query()->where('role', 'tenant')->findOrFail($value);
        });

        Route::bind('adminUser', function (string $value) {
            return User::query()->where('role', 'admin')->findOrFail($value);
        });
    }
}
