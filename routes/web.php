<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    Route::get('/logout', fn () => Inertia::render('Auth/Logout'))->name('logout.confirm');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Sección: Mis Tickets
    Route::middleware('permission:mis_tickets')->prefix('mis-tickets')->name('mis-tickets.')->group(function (): void {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/crear', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
    });

    // Sección: Mesa de Servicio
    Route::middleware('permission:mesa_servicio')->prefix('mesa-servicio')->name('mesa-servicio.')->group(function (): void {
        Route::get('/', fn () => Inertia::render('MesaServicio/Index'))->name('index');
    });

    // Sección: Reportes
    Route::middleware('permission:reportes')->prefix('reportes')->name('reportes.')->group(function (): void {
        Route::get('/', fn () => Inertia::render('Reportes/Index'))->name('index');
    });

    // Sección: Administración
    Route::middleware('permission:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/', fn () => Inertia::render('Admin/Index'))->name('index');
    });
});
