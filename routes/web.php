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

    //Eventos
    Route::resource('eventos', EventoController::class)->parameters([
        'evento' => 'evento'
    ])->except(['index', 'show']);

    // Calendario
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
});
