<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePendingRegistrationRequest;
use App\Http\Requests\UpdatePendingRegistrationRequest;
use App\Models\PendingRegistration;
use Illuminate\Support\Facades\Hash;
use App\Http\Tools\Tools;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyRegistrationEmail;
use Exception;

class PendingRegistrationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StorePendingRegistrationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePendingRegistrationRequest $request)
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
            'phone' => $request->phone,
            'rol' => strtolower($request->rol),
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
        $response['message'] = 'Se envió correctamente el enlace de verificación al Email, el usuario será creado una vez verificado.';
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PendingRegistration  $pendingRegistration
     * @return \Illuminate\Http\Response
     */
    public function show(PendingRegistration $pendingRegistration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PendingRegistration  $pendingRegistration
     * @return \Illuminate\Http\Response
     */
    public function edit(PendingRegistration $pendingRegistration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePendingRegistrationRequest  $request
     * @param  \App\Models\PendingRegistration  $pendingRegistration
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePendingRegistrationRequest $request, PendingRegistration $pendingRegistration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PendingRegistration  $pendingRegistration
     * @return \Illuminate\Http\Response
     */
    public function destroy(PendingRegistration $pendingRegistration)
    {
        $response = ['ok' => false, 'message' => ''];
        $status = 404;
        try{
            if (!$pendingRegistration) {
                $response['message'] = 'Registro pendiente no encontrado.';
            }else{
                $status = 200;
                $response['ok'] = true;
                $response['message'] = 'Registro pendiente eliminado correctamente.';
                $pendingRegistration->delete();
            }
        }catch(Exception $e){
            $status = 500;
            $response['message'] = 'Error al eliminar el registro pendiente.';
        }
        return response()->json($response, $status);
    }
}
