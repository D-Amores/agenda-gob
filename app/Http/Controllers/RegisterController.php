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

        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'area_id' => ['required', 'exists:c_area,id'],
        ], [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números, puntos, guiones y guiones bajos.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe contener al menos: 1 letra minúscula, 1 mayúscula, 1 número y 1 carácter especial (@$!%*?&).',
            'area_id.required' => 'Debe seleccionar un área.',
            'area_id.exists' => 'El área seleccionada no es válida.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Crear el usuario
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'area_id' => $request->area_id,
        ]);

        // Disparar evento para enviar email de verificación automáticamente
        event(new Registered($user));

        // Iniciar sesión temporal para poder mostrar la página de verificación
        Auth::login($user);

        // Redirigir a la página de verificación de email
        return redirect()->route('verification.notice')->with('success', 'Cuenta creada exitosamente. Hemos enviado un enlace de verificación a tu correo electrónico.');
    }
}
