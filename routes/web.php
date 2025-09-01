<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudienciaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisterController;

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
    // Login routes
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    
    // Register routes
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])
        ->middleware(['throttle.registration', 'throttle:3,1'])
        ->name('register.submit');
});

// Email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard')->with('success', '¡Email verificado exitosamente!');
    })->middleware(['signed'])->name('verification.verify');
    
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Enlace de verificación enviado!');
    })->middleware('throttle:6,1')->name('verification.send');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [LogoutController::class, 'store'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');

    // Perfil (URIs: profile/{user}/edit, profile/{user})
    Route::resource('profile', ProfileController::class)->parameters(['profile' => 'user'])
        ->only(['edit', 'update']);

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
