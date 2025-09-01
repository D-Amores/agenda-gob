<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Mostrar formulario (accesible sin autenticación)
    public function index()
    {
        return view('auth.login');
    }

    // Procesar login (accesible sin autenticación)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Verificar si el email está verificado
            $user = Auth::user();
            
            // Forzar recarga del usuario desde la base de datos para asegurar datos actualizados
            $freshUser = $user->fresh();
            
            if (!$freshUser->hasVerifiedEmail()) {
                // Si el usuario no tiene email verificado, algo está mal
                // En el nuevo sistema, solo se crean usuarios con email verificado
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'username' => 'Tu cuenta no está verificada. Por favor, contacta al administrador.',
                ]);
            }
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Usuario o contraseña incorrectos.',
        ])->onlyInput('username');
    }

    // Logout (requiere autenticación)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}