<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MesaServicio\TicketsController as MesaServicioTicketsController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Solicitante\TicketsController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'usuario_activo'])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/perfil',  [PerfilController::class, 'index'])->name('perfil');
    Route::put('/perfil',  [PerfilController::class, 'update'])->name('perfil.update');

    Route::prefix('mds')->name('mesadeservicio.')->group(function () {
        Route::get('/tickets',            [MesaServicioTicketsController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/crear',      [MesaServicioTicketsController::class, 'crearVista'])->name('tickets.crear.vista');
        Route::post('/tickets',           [MesaServicioTicketsController::class, 'crearTicket'])->name('tickets.crear');
        Route::get('/tickets/{ticket}',            [MesaServicioTicketsController::class, 'ver'])->name('tickets.ver');
        Route::get('/tickets/{ticket}/clasificar', [MesaServicioTicketsController::class, 'clasificarVista'])->name('tickets.clasificar.vista');
        Route::post('/tickets/{ticket}/clasificar',[MesaServicioTicketsController::class, 'clasificar'])->name('tickets.clasificar');
        Route::get('/trabajadores/buscar',[MesaServicioTicketsController::class, 'buscarTrabajador'])->name('trabajadores.buscar');
    });

    Route::prefix('mt')->name('solicitante.')->group(function () {
        Route::get('/',                              [TicketsController::class, 'index'])->name('index');
        Route::get('/crear',                         [TicketsController::class, 'crearVista'])->name('tickets.crear');
        Route::post('/crear',                        [TicketsController::class, 'crearTicket'])->name('crear');
        Route::get('/tickets/{ticket}',              [TicketsController::class, 'verVista'])->name('tickets.ver');
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
