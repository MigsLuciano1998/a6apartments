<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PayableController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomPayableController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Tenant\AnnouncementController as TenantAnnouncementController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->to(auth()->user()->homeUrl());
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::resource('rooms', RoomController::class);
    Route::post('rooms/{room}/assign-tenant', [RoomController::class, 'assignTenant'])->name('rooms.assign-tenant');
    Route::post('rooms/{room}/remove-tenant', [RoomController::class, 'removeTenant'])->name('rooms.remove-tenant');
    Route::post('rooms/{room}/payables', [RoomPayableController::class, 'store'])->name('rooms.payables.store');
    Route::put('rooms/{room}/payables/{roomPayable}', [RoomPayableController::class, 'update'])->name('rooms.payables.update');
    Route::post('rooms/{room}/payables/{roomPayable}/end', [RoomPayableController::class, 'end'])->name('rooms.payables.end');
    Route::delete('rooms/{room}/payables/{roomPayable}', [RoomPayableController::class, 'destroy'])->name('rooms.payables.destroy');
    Route::resource('tenants', TenantController::class);
    Route::resource('payables', PayableController::class);
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/tenants/{tenant}/payables', [PaymentController::class, 'tenantPayables'])->name('payments.tenant-payables');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::resource('expenses', ExpenseController::class);
    Route::resource('announcements', AnnouncementController::class)->except(['show']);

    Route::middleware('role:manager')->group(function () {
        Route::resource('admins', AdminUserController::class)
            ->parameters(['admins' => 'adminUser']);
    });
});

Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'tenant'])->name('dashboard');
    Route::get('/payments', [TenantPaymentController::class, 'index'])->name('payments.index');
    Route::get('/announcements', [TenantAnnouncementController::class, 'index'])->name('announcements.index');
});
