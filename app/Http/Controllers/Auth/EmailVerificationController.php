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
            
            return redirect('/dashboard')->with('success', '¡Email verificado exitosamente! Te hemos enviado tu contraseña por email.');
        }
        
        return redirect('/dashboard')->with('success', '¡Email verificado exitosamente!');
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
