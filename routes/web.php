<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\EmailVerificationRegistrationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AudienciaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ChangeEstatusController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Admin\UsersController;

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

Route::get('/calendario/pdf', [PdfController::class, 'download'])->name('calendario.pdf');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle.registration')->name('login.submit');

    // Register routes
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])
        ->middleware('throttle.registration')
       ->name('register.submit');
    
    // Registration verification routes
    Route::get('/registration/pending', [EmailVerificationRegistrationController::class, 'pending'])->name('registration.pending');
    Route::get('/registration/verify/{token}', [EmailVerificationRegistrationController::class, 'verify'])->name('registration.verify');
    
    // Password reset routes
    Route::get('/forgot-password', [PasswordResetController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendNewPassword'])
        ->middleware('throttle.registration')
        ->name('password.send');
});


Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LogoutController::class, 'store'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');

    // Perfil (URIs: profile/{user}/edit, profile/{user})
    Route::resource('profile', ProfileController::class)->parameters(['profile' => 'user'])->only(['edit', 'update']);
    Route::put('/profile/{user}/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Audiencias
    Route::resource('audiencias', AudienciaController::class)->parameters(['audiencia' => 'audiencia'])->except(['index', 'show']);

    //Eventos
    Route::resource('eventos', EventoController::class)->parameters([
        'evento' => 'evento'
    ])->except(['index', 'show']);

    // Calendario
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');

    // Cambiar estatus
    Route::put('/change-estatus/{model}/{id}', [ChangeEstatusController::class, 'update'])->name('estatus.update');

    Route::middleware(['auth', 'role:admin'])->group(function () {
        // Administración (solo para admins)
        Route::resource('admin/users', UsersController::class)->parameters(['admin' => 'user'])->only(['index', 'store', 'update', 'destroy']);
        // API protegida solo para admins
        Route::post('/admin/users/users-api', [UsersController::class, 'users_api'])->name('users.api');
    });
    

    /*
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/panel', [AdminController::class, 'panel'])->name('panel');
        Route::post('/usuarios', [AdminController::class, 'registrarUsuario'])->name('registrar-usuario');
        Route::get('/usuarios/{usuario}/edit', [AdminController::class, 'editarUsuario'])->name('editar-usuario');
        Route::put('/usuarios/{usuario}', [AdminController::class, 'actualizarUsuario'])->name('actualizar-usuario');
        Route::delete('/usuarios/{usuario}', [AdminController::class, 'eliminarUsuario'])->name('eliminar-usuario');
    });
    */
});