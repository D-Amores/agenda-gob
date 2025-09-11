<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\PendingRegistration;
use App\Models\Area;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\PasswordDeliveryNotification;
use Spatie\Permission\Models\Role;
use App\Http\Tools\Tools;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyRegistrationEmail;
use Exception;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }


    public function users_api(Request $request)
    {
        $status = 200;
        $response = [
            'ok' => true,
            'icono' => 'success',
            'mensaje' => 'Éxito'
        ];

        try {
            $method = $request->input('method');

            if (!$method || !in_array($method, ['get'])) {
                return response()->json([
                    'ok' => false,
                    'icono' => 'warning',
                    'mensaje' => 'Método inválido o no proporcionado.'
                ], 400);
            }

            switch ($method) {
                case 'show':
                    // if (empty($request->id)) {
                    //     throw new Exception('Parámetro id no proporcionado.');
                    // }

                    // $data = User::find($request->id);
                    // $response['data'] = $data;
                    break;

                case 'get':
                    // Lista de usuarios y registros pendientes
                    $users = User::all();
                    $pending = PendingRegistration::all();

                    $response['data'] = [
                        'users' => $users,
                        'pending_registrations' => $pending,
                    ];
                    break;
            }

        } catch (Exception $e) {
            $status = 500;
            $response = [
                'codigo' => -1,
                'icono' => 'error',
                'mensaje' => $e->getMessage()
            ];
        }

        return response()->json($response, $status);
    }

     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('admin.panel');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        $status = 422;   

        PendingRegistration::expired()->delete();

        // Generar contraseña segura automáticamente
        $generatedPassword = Tools::generateSecurePassword();
        $verificationToken = PendingRegistration::generateVerificationToken();

        // Crear registro pendiente (no crear usuario aún)
        $pendingRegistration = new PendingRegistration([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($generatedPassword),
            'area_id' => $request->area_id,
            'verification_token' => $verificationToken,
            'expires_at' => now()->addDay(), // Expira en 24 horas
        ]);

        // Verificar duplicados
        if ($pendingRegistration->isExist()) {
            $response['message'] = 'El usuario o correo ya está en proceso de registro o ya existe.';
            return response()->json($response, $status);
        }
        $pendingRegistration->save();

        // Crear URL de verificación
        $verificationUrl = route('registration.verify', ['token' => $verificationToken]);

        // Enviar email de verificación
        Notification::route('mail', $request->email)->notify(new VerifyRegistrationEmail($pendingRegistration, $verificationUrl));

        $response['ok'] = true;
        $response['message'] = 'Se envió correctamente el enlace de verificación.';
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
