<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PasswordResetRequest;
use App\Models\User;
use App\Notifications\NewPasswordNotification;
use App\Http\Controllers\Controller;


class PasswordResetController extends Controller
{
    /**
     * Mostrar formulario de recuperación de contraseña
     */
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Procesar solicitud de recuperación
     */
    public function sendNewPassword(PasswordResetRequest $request)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null, 'data' => null];
        // La validación ya se maneja en PasswordResetRequest

        
        // Buscar el usuario
        $user = User::where('email', $request->email)->first();

        // Verificar que el email esté verificado
        if (!$user->hasVerifiedEmail()) {

            $response['message'] = 'Esta cuenta no tiene el email verificado. Por favor, contacta al administrador.';
            $response['errors'] = ['email' => [$response['message']]];
            return response()->json($response, 422);
        }

        // Generar nueva contraseña
        $newPassword = $this->generateSecurePassword();

        // Actualizar la contraseña del usuario
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Enviar la nueva contraseña por email
        $user->notify(new NewPasswordNotification($newPassword));

        // Respuesta exitosa
        $response['ok'] = true;
        $response['message'] = 'Hemos enviado una nueva contraseña a tu correo electrónico.';
        $response['data'] = [
            'email' => $user->email,
            'redirect_url' => route('login')
        ];

        return response()->json($response, 200);
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
