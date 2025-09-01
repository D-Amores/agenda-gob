<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\Area;

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
    public function register(Request $request)
    {
        // Verificar honeypot (anti-bot)
        if ($request->filled('website')) {
            return back()->withErrors(['security' => 'Solicitud no válida.'])->withInput();
        }

        // Validar los datos del formulario (sin contraseña)
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'area_id' => ['required', 'exists:c_area,id'],
        ], [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números, puntos, guiones y guiones bajos.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'area_id.required' => 'Debe seleccionar un área.',
            'area_id.exists' => 'El área seleccionada no es válida.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generar contraseña segura automáticamente
        $generatedPassword = $this->generateSecurePassword();

        // Crear el usuario con la contraseña generada
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($generatedPassword),
            'area_id' => $request->area_id,
        ]);

        // Guardar la contraseña en la sesión temporalmente para enviarla después de verificación
        session(['temp_password_' . $user->id => $generatedPassword]);

        // Disparar evento para enviar email de verificación automáticamente
        event(new Registered($user));

        // Iniciar sesión temporal para poder mostrar la página de verificación
        Auth::login($user);

        // Redirigir a la página de verificación de email
        return redirect()->route('verification.notice')->with('success', 'Cuenta creada exitosamente. Hemos enviado un enlace de verificación a tu correo electrónico.');
    }
}
