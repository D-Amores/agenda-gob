<?php

use App\Http\Controllers\AudienciaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

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

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
//Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto');


//Audiencia
Route::get('/audiencia', [AudienciaController::class, 'index'])->name('audiencia_registro');
