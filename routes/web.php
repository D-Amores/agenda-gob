<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudienciaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;

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

// Mostrar el formulario
Route::get('/login', [LoginController::class, 'index'])->name('login');

// Procesar login
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Dashboard protegido
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

//Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto');


//Audiencia
Route::get('/audiencia', [AudienciaController::class, 'index'])->name('audiencias.registro');
Route::post('/audiencia/guardar', [AudienciaController::class, 'store'])->name('audiencias.store');
// Mostrar formulario de edición
Route::get('/audiencia/{audiencia}/editar', [AudienciaController::class, 'editar'])->name('audiencias.editar');
// Actualizar audiencia (POST o PUT)
Route::put('/audiencia/{audiencia}', [AudienciaController::class, 'actualizar'])->name('audiencias.actualizar');



// Calendario
Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
//rutas de ejemplo para eliminar
Route::delete('/audiencia/eliminar', [CalendarioController::class, 'destroyAudiencia'])->name('audiencias.eliminar');
Route::delete('/evento/eliminar', [CalendarioController::class, 'destroyEvento'])->name('eventos.eliminar');