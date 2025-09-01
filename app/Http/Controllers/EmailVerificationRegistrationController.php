<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Notifications\PasswordDeliveryNotification;

class EmailVerificationRegistrationController extends Controller
{
    /**
     * Verificar el email y crear el usuario
     */
    public function verify(Request $request, $token)
    {
        // Buscar el registro pendiente
        $pendingRegistration = PendingRegistration::where('verification_token', $token)
            ->notExpired()
            ->first();

        if (!$pendingRegistration) {
            return redirect()->route('register')
                ->withErrors(['verification' => 'El enlace de verificación es inválido o ha expirado.']);
        }

        try {
            // Crear el usuario definitivo
            $user = User::create([
                'username' => $pendingRegistration->username,
                'email' => $pendingRegistration->email,
                'password' => $pendingRegistration->password, // Ya está hasheada
                'area_id' => $pendingRegistration->area_id,
                'email_verified_at' => now(),
            ]);

            // Verificar que el email se marcó como verificado
            $user->markEmailAsVerified();
            
            // Debug: verificar el estado
            Log::info('Usuario creado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'hasVerifiedEmail' => $user->hasVerifiedEmail()
            ]);

            // Generar una nueva contraseña para enviar al usuario
            $newPassword = $this->generateSecurePassword();
            $user->update(['password' => Hash::make($newPassword)]);

            // Enviar la contraseña por email
            $user->notify(new PasswordDeliveryNotification($newPassword));

            // Eliminar el registro pendiente
            $pendingRegistration->delete();

            // Limpiar registros expirados (housekeeping)
            PendingRegistration::expired()->delete();

            return redirect()->route('login')
                ->with('success', 'Email verificado exitosamente. Hemos enviado tu contraseña a tu correo electrónico.');

        } catch (\Exception $e) {
            return redirect()->route('register')
                ->withErrors(['verification' => 'Error al procesar la verificación. Por favor, inténtalo de nuevo.']);
        }
    }

    /**
     * Mostrar página de verificación pendiente
     */
    public function pending()
    {
        return view('auth.registration-pending');
    }

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
}
