<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\PasswordDeliveryNotification;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Mostrar el panel de administración
     */
    public function panel()
    {
        $usuarios = User::with(['area', 'roles'])->paginate(10);
        $areas = Area::orderBy('area')->get();
        $roles = Role::all();
        
        return view('admin.panel', compact('usuarios', 'areas', 'roles'));
    }

    /**
     * Mostrar la vista para crear usuario
     */
    public function crearUsuario()
    {
        $areas = Area::orderBy('area')->get();
        $roles = Role::all();
        
        return view('admin.crear-usuario', compact('areas', 'roles'));
    }

    /**
     * Registrar un nuevo usuario
     */
    public function registrarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'area_id' => 'nullable|exists:c_area,id',
            'rol' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // Generar contraseña segura
            $password = $this->generateSecurePassword();
            
            // Crear usuario
            $usuario = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($password),
                'area_id' => $request->area_id,
                'phone' => $request->phone,
                'email_verified_at' => now(), // Admin puede crear usuarios ya verificados
            ]);

            // Asignar rol
            $usuario->assignRole($request->rol);

            // Enviar contraseña por email
            $usuario->notify(new PasswordDeliveryNotification($password));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario creado exitosamente. Se ha enviado la contraseña por correo.',
                    'user' => [
                        'id' => $usuario->id,
                        'name' => $usuario->name,
                        'email' => $usuario->email,
                        'username' => $usuario->username,
                        'rol' => $usuario->getRoleNames()->first(),
                        'area' => $usuario->area->area ?? null,
                    ]
                ]);
            }

            return redirect()->route('admin.panel')->with('success', 'Usuario creado exitosamente. Se ha enviado la contraseña por correo.');
            
        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el usuario.',
                    'errors' => ['general' => ['Ocurrió un error inesperado.']]
                ], 500);
            }

            return back()->withErrors(['general' => 'Ocurrió un error al crear el usuario.'])->withInput();
        }
    }

    /**
     * Editar un usuario existente
     */
    public function editarUsuario(User $usuario)
    {
        $areas = Area::orderBy('area')->get();
        $roles = Role::all();
        
        return response()->json([
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'username' => $usuario->username,
                'email' => $usuario->email,
                'phone' => $usuario->phone,
                'area_id' => $usuario->area_id,
                'rol' => $usuario->getRoleNames()->first(),
            ],
            'areas' => $areas,
            'roles' => $roles
        ]);
    }

    /**
     * Actualizar un usuario
     */
    public function actualizarUsuario(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'username' => 'required|string|max:50|unique:users,username,' . $usuario->id,
            'area_id' => 'nullable|exists:c_area,id',
            'rol' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $usuario->update([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'area_id' => $request->area_id,
                'phone' => $request->phone,
            ]);

            // Actualizar rol si cambió
            if ($usuario->getRoleNames()->first() !== $request->rol) {
                $usuario->syncRoles([$request->rol]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente.',
                ]);
            }

            return redirect()->route('admin.panel')->with('success', 'Usuario actualizado exitosamente.');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el usuario.',
                ], 500);
            }

            return back()->withErrors(['general' => 'Ocurrió un error al actualizar el usuario.'])->withInput();
        }
    }

    /**
     * Eliminar un usuario
     */
    public function eliminarUsuario(User $usuario)
    {
        try {
            // No permitir que el admin se elimine a sí mismo
            if ($usuario->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminarte a ti mismo.',
                ], 400);
            }

            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente.',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario.',
            ], 500);
        }
    }

    /**
     * Generar contraseña segura
     */
    private function generateSecurePassword($length = 12)
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';
        
        $password = '';
        
        // Asegurar al menos un carácter de cada tipo
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Completar con caracteres aleatorios
        $allChars = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mezclar la contraseña
        return str_shuffle($password);
    }
}
