<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'roles' => auth()->user()->getRoleNames()->values(),
            'permissions' => auth()->user()->getPermissionNames()->values(),
            'logoutUrl' => route('logout.confirm'),
        ]);
    })->name('dashboard');

    Route::get('/logout', function () {
        return Inertia::render('Auth/Logout', [
            'dashboardUrl' => route('dashboard'),
            'logoutUrl' => route('logout'),
        ]);
    })->name('logout.confirm');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
