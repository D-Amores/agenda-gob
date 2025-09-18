<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PendingRegistration;
use App\Services\PendingRegistrationService;
use App\Http\Controllers\Controller;


class EmailVerificationRegistrationController extends Controller
{
    /**
     * Verificar el email y crear el usuario
     */
    public function verify(Request $request, $token, PendingRegistrationService $service)
    {
        $response = ['ok' => false, 'message' => ''];
        
        // Buscar el registro pendiente
        $pendingRegistration = PendingRegistration::where('verification_token', $token)->notExpired()->first();

        if (!$pendingRegistration) {
            $response['message'] = 'El enlace de verificación es inválido o ha expirado.';
            $response['errors'] = ['verification' => 'El enlace de verificación es inválido o ha expirado.'];
            
            return response()->json($response, 400);
        }
        $user = null;
        try {
            // Crear el usuario definitivo
            $user = $service->confirm($pendingRegistration);

            $response['ok'] = true;
            $response['message'] = 'Email verificado exitosamente. Hemos enviado tu contraseña a tu correo electrónico.';
            $status = 200;

        } catch (\Exception $e) {
            Log::error('Error en verificación de email', [
                'error' => $e->getMessage(),
                'token' => $token
            ]);
            
            $response['message'] = 'Error al procesar la verificación. Por favor, inténtalo de nuevo.';
            $response['errors'] = ['verification' => 'Error al procesar la verificación. Por favor, inténtalo de nuevo.'];
            $status = 500;
        }
        if($request->expectsJson()) {
            return response()->json($response, $status);
        }
        return view('auth.email-verification', [
                'success' => $response['ok'],
                'message' => $response['message'],
                'email' => $user->email??'',
                'redirect_url' => route('login'),
                'token' => $token
            ]);
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
}
