<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
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
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        //
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
        if (!$user) {
            abort(404);
        }

        if ($user->id !== auth()->id()) {
            return redirect()->route('profile.edit', auth()->user());
        }

        return view('profile.edit', compact('user'));
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
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        if ($user->id !== auth()->id()) {
            $response['message'] = 'No autorizado.';
            return response()->json($response, 403);
        }

        try {
            $data = $request->only(['username']);

            if ($request->hasFile('profile_photo')) {
                // elimina la foto anterior si existe
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $request->file('profile_photo')->store('profiles', 'public');
                $data['profile_photo_path'] = $path;
            }
            $user->update($data);
            $response['ok'] = true;
            $response['message'] = 'Perfil actualizado correctamente.';
            return response()->json($response);
        } catch (\Throwable $e) {
            report($e);
            $response['message'] = 'No se pudo actualizar el perfil.';
            return response()->json($response, 500);
        }
    }

    /**
     * Cambiar contraseña del usuario
     *
     * @param  \App\Http\Requests\ChangePasswordRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request, User $user)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        // Verificar que el usuario solo pueda cambiar su propia contraseña
        if ($user->id !== auth()->id()) {
            
            $response['message'] = 'No tienes permisos para cambiar esta contraseña.';
            return response()->json($response, 403);
        }

        try {            
            // Actualizar la contraseña
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            $response['ok'] = true;
            $response['message'] = 'Contraseña cambiada exitosamente.';
            return response()->json($response, 200);

        } catch (\Exception $e) {
            report($e);
            $response['message'] = 'Error al cambiar la contraseña. Por favor, inténtalo de nuevo.';
            return response()->json($response, 500);
        }
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
