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
        $response = ['ok' => false, 'message' => '', 'errors' => null, 'data' => null];
        
        // Buscar el registro pendiente
        $pendingRegistration = PendingRegistration::where('verification_token', $token)
            ->notExpired()
            ->first();

        if (!$pendingRegistration) {
            $response['message'] = 'El enlace de verificación es inválido o ha expirado.';
            $response['errors'] = ['verification' => 'El enlace de verificación es inválido o ha expirado.'];
            
            if ($request->expectsJson()) {
                return response()->json($response, 400);
            }
            
            // Para acceso directo desde enlaces, mostrar la vista de verificación
            return view('auth.email-verification', [
                'error' => true,
                'message' => 'El enlace de verificación es inválido o ha expirado.',
                'token' => $token
            ]);
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

            $response['ok'] = true;
            $response['message'] = 'Email verificado exitosamente. Hemos enviado tu contraseña a tu correo electrónico.';
            $response['data'] = [
                'user_id' => $user->id,
                'email' => $user->email,
                'redirect_url' => route('login')
            ];

            if ($request->expectsJson()) {
                return response()->json($response);
            }

            // Para acceso directo desde enlaces, mostrar la vista de verificación con éxito
            return view('auth.email-verification', [
                'success' => true,
                'message' => 'Email verificado exitosamente. Hemos enviado tu contraseña a tu correo electrónico.',
                'email' => $user->email,
                'redirect_url' => route('login'),
                'token' => $token
            ]);

        } catch (\Exception $e) {
            Log::error('Error en verificación de email', [
                'error' => $e->getMessage(),
                'token' => $token
            ]);
            
            $response['message'] = 'Error al procesar la verificación. Por favor, inténtalo de nuevo.';
            $response['errors'] = ['verification' => 'Error al procesar la verificación. Por favor, inténtalo de nuevo.'];
            
            if ($request->expectsJson()) {
                return response()->json($response, 500);
            }
            
            // Para acceso directo desde enlaces, mostrar la vista de verificación con error
            return view('auth.email-verification', [
                'error' => true,
                'message' => 'Error al procesar la verificación. Por favor, inténtalo de nuevo.',
                'token' => $token
            ]);
        }
    }

    /**
     * Mostrar página de verificación pendiente
     */
    public function pending(Request $request)
    {
        if ($request->expectsJson()) {
            $response = [
                'ok' => true, 
                'message' => 'Verificación pendiente', 
                'errors' => null, 
                'data' => [
                    'status' => 'pending',
                    'title' => '¡Verifica tu correo electrónico! 📧',
                    'description' => 'Hemos enviado un enlace de verificación a tu correo electrónico.',
                    'instructions' => [
                        'Revisa tu bandeja de entrada',
                        'Haz clic en el enlace de verificación',
                        'Tu cuenta será creada automáticamente',
                        'Recibirás tu contraseña por email'
                    ],
                    'email' => session('email'),
                    'expiration_info' => 'El enlace expira en 24 horas',
                    'links' => [
                        'login' => route('login'),
                        'register' => route('register')
                    ]
                ]
            ];
            
            return response()->json($response);
        }
        
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
