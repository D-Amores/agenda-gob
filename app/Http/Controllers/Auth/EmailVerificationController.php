<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PasswordGenerated;

class EmailVerificationController extends Controller
{
    /**
     * Mostrar la página de verificación de email
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Verificar el email y enviar la contraseña generada
     */
    public function verify(EmailVerificationRequest $request)
    {
        $user = $request->user();
        
        // Marcar el email como verificado
        $request->fulfill();
        
        // Obtener la contraseña temporal de la sesión
        $tempPassword = session('temp_password_' . $user->id);
        
        if ($tempPassword) {
            // Enviar la contraseña por email
            $user->notify(new PasswordGenerated($tempPassword));
            
            // Limpiar la contraseña temporal de la sesión
            session()->forget('temp_password_' . $user->id);
            
            // Cerrar sesión para que el usuario tenga que usar la nueva contraseña
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('success', '¡Email verificado exitosamente! Te hemos enviado tu contraseña por email. Revisa tu bandeja de entrada e inicia sesión.');
        }
        
        // Si no hay contraseña temporal, también cerrar sesión y enviar al login
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', '¡Email verificado exitosamente! Ya puedes iniciar sesión.');
    }

    /**
     * Reenviar el email de verificación
     */
    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Enlace de verificación enviado!');
    }
}
