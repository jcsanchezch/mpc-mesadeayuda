<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Solicitante\TicketsController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'usuario_activo'])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');

    Route::prefix('solicitante')->name('solicitante.')->group(function () {
        Route::get('/',                              [TicketsController::class,      'index'])->name('index');
        Route::post('/crear',                        [TicketsController::class,      'crearTicket'])->name('crear');
        Route::post('/tickets/{ticket}/conformidad', [TicketsController::class, 'conformidad'])->name('tickets.conformidad');
    });

});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])
        ->name('logout');
});
