<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Area;
use App\Models\PendingRegistration;
use App\Notifications\VerifyRegistrationEmail;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /**
     * Generar una contraseña segura automáticamente
     */
    private function generateSecurePassword($length = 12)
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '@$!%*?&';
        
        // Asegurar que tenga al menos uno de cada tipo
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Llenar el resto con caracteres aleatorios
        $allChars = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mezclar los caracteres
        return str_shuffle($password);
    }

    /**
     * Mostrar el formulario de registro
     */
    public function index()
    {
        $areas = Area::all(); // Para el dropdown de áreas
        return view('auth.register', compact('areas'));
    }

    /**
     * Procesar el registro del usuario
     */
    public function register(RegisterRequest $request)
    {
        // Limpiar registros expirados antes de crear uno nuevo
        PendingRegistration::expired()->delete();

        // Generar contraseña segura automáticamente
        $generatedPassword = $this->generateSecurePassword();

        // Generar token de verificación único
        $verificationToken = PendingRegistration::generateVerificationToken();

        // Crear registro pendiente (no crear usuario aún)
        $pendingRegistration = PendingRegistration::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($generatedPassword),
            'area_id' => $request->area_id,
            'verification_token' => $verificationToken,
            'expires_at' => Carbon::now()->addDay(), // Expira en 24 horas
        ]);

        // Crear URL de verificación
        $verificationUrl = route('registration.verify', ['token' => $verificationToken]);

        // Enviar email de verificación
        Notification::route('mail', $request->email)
            ->notify(new VerifyRegistrationEmail($pendingRegistration, $verificationUrl));

        // Responder según el tipo de request
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hemos enviado un enlace de verificación a tu correo electrónico. Por favor, revisa tu bandeja de entrada y haz clic en el enlace para completar tu registro.',
                'data' => [
                    'email' => $request->email,
                    'redirect_url' => route('registration.pending')
                ]
            ], 200);
        }

        // Redirigir a página de verificación pendiente (fallback para requests no-AJAX)
        return redirect()->route('registration.pending')
            ->with('success', 'Hemos enviado un enlace de verificación a tu correo electrónico. Por favor, revisa tu bandeja de entrada y haz clic en el enlace para completar tu registro.')
            ->with('email', $request->email);
    }
}
