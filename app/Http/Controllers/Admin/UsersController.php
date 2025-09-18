<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\PendingRegistration;
use App\Models\Area;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\StoreUserRequest;
use App\Services\PendingRegistrationService;

class UsersController extends Controller
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
            'message' => 'Éxito'
        ];

        try {
            $method = $request->input('method');

            if (!$method || !in_array($method, ['get'])) {
                return response()->json([
                    'ok' => false,
                    'icono' => 'warning',
                    'message' => 'Método inválido o no proporcionado.'
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
                    $users = User::with('roles', 'area')->get();
                    $pending = PendingRegistration::with('area')->get();
                    $roles = Role::all();
                    $areas = Area::all();

                    $response['data'] = [
                        'users' => $users,
                        'pending_registrations' => $pending,
                        'roles' => $roles,
                        'areas' => $areas,
                    ];
                    break;
            }

        } catch (Exception $e) {
            $status = 500;
            $response = [
                'codigo' => -1,
                'icono' => 'error',
                'message' => $e->getMessage()
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
    public function store(StoreUserRequest $request, PendingRegistrationService $pendingRegistrationService)
    {
        $response = ['ok'=>false, 'message'=>''];
        $status = 422;

        $pendingRegistration = PendingRegistration::find($request->id);
        if (!$pendingRegistration) {
            $response['message'] = 'Registro pendiente no encontrado.';
            $status = 404;
            return response()->json($response, $status);
        }
        try{

            // Crear usuario a partir del registro pendiente
            $user = $pendingRegistrationService->confirm($pendingRegistration);

            $response['ok'] = true;
            $response['message'] = "Usuario creado correctamente el usuario: {$user->username}. Se ha enviado la contraseña al correo electrónico.";
            $status = 201;

        }catch(Exception $e){
            Log::error('Error al crear usuario: ' . $e->getMessage());
            $response['message'] = 'Error al crear el usuario.';
            $status = 500;
        }
        return response()->json($response, $status);
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
        $response = ['ok' => false, 'message' => ''];
        $status = 422;
        if (!$user) {
            $response['message'] = 'Usuario no encontrado.';
            $status = 404;
            return response()->json($response, $status);
        }

        if ($user->id === auth()->id()) {
            $response['message'] = 'No puedes modificar tu propio usuario aquí.';
            $status = 403;
            return response()->json($response, $status);
        }
        
        try{
            $data = $request->only(['username', 'area_id']);
            $user->update($data);

            if ($request->filled('rol')) {
                $user->syncRoles([$request->rol]);
            }
            $response['ok'] = true;
            $response['message'] = 'Usuario actualizado correctamente.';
            $status = 200;
    
        } catch (Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            $response['message'] = 'Error al actualizar el usuario.';
            $status = 500;
        }
        return response()->json($response, $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $response = ['ok' => false, 'message' => ''];
        $status = 200;

        try{
            if (!$user) {
                $response['message'] = 'Usuario no encontrado.';
                $status = 404;
                return response()->json($response, $status);
            }

            if ($user->id === auth()->id()) {
                $response['message'] = 'No puedes eliminar tu propio usuario.';
                $status = 403;
                return response()->json($response, $status);
            }
            $user->delete();
            $response['ok'] = true;
            $response['message'] = 'Usuario eliminado correctamente.';
        }catch(Exception $e){
            Log::error('Error al eliminar usuario: ' . $e->getMessage());
            $response['message'] = 'Error al eliminar el usuario.';
            $status = 500;
        }
        return response()->json($response, $status);
    }
}
