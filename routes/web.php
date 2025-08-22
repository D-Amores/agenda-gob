<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudienciaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    // Mostrar el formulario
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});


Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'store'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Audiencias
    Route::resource('audiencias', AudienciaController::class)->parameters([
        'audiencia' => 'audiencia'
    ])->except(['index', 'show']);

    // Audiencias
    /*Route::get('/audiencia', [AudienciaController::class, 'registrar'])->name('audiencias.registro');
    Route::middleware('permission:crear audiencia')->group(function () {
        Route::post('/audiencia/guardar', [AudienciaController::class, 'crear'])->name('audiencias.store');
    });
    Route::middleware('permission:editar audiencia')->group(function () {
        Route::get('/audiencia/{audiencia}/editar', [AudienciaController::class, 'editar'])->name('audiencias.editar');
        Route::put('/audiencia/{audiencia}', [AudienciaController::class, 'actualizar'])->name('audiencias.actualizar');
    });
    Route::middleware('permission:eliminar audiencia')->group(function () {
        Route::delete('/audiencia/eliminar/{audiencia}', [AudienciaController::class, 'eliminar'])->name('audiencias.eliminar');
    });
    */


    // Calendario
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');

    //Eventos
    Route::get('/evento', [EventoController::class, 'registrar'])->name('eventos.registro');
    Route::post('/evento/guardar', [EventoController::class, 'crear'])->name('eventos.store');
    Route::get('/evento/{evento}/editar', [EventoController::class, 'editar'])->name('eventos.editar');
    Route::put('/evento/{evento}', [EventoController::class, 'actualizar'])->name('eventos.actualizar');
    Route::delete('/evento/eliminar/{evento}', [EventoController::class, 'eliminar'])->name('eventos.eliminar');
});
