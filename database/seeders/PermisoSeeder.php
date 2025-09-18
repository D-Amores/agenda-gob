<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermisoSeeder extends Seeder
{
    public function run()
    {
        // Limpia la caché de permisos y roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos para el sistema
        $permisos = [
            // Gestión de usuarios (solo admin)
            'gestionar usuarios',
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            
            // Eventos y audiencias (ambos roles)
            'ver evento',
            'crear evento',
            'editar evento',
            'eliminar evento',
            'ver audiencia',
            'crear audiencia',
            'editar audiencia',
            'eliminar audiencia',
            
            // Panel de administración
            'acceder panel admin'
        ];

        // Crear permisos si no existen
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Asignar todos los permisos al admin
        $admin->syncPermissions(Permission::all());

        // Usuario regular: puede gestionar eventos y audiencias, pero no usuarios
        $permisosUser = [
            'ver evento',
            'crear evento',
            'editar evento',
            'eliminar evento',
            'ver audiencia',
            'crear audiencia',
            'editar audiencia',
            'eliminar audiencia'
        ];
        $user->syncPermissions($permisosUser);

        // Asignar rol de admin al primer usuario (si existe)
        $primerUsuario = User::first();
        if ($primerUsuario && !$primerUsuario->hasAnyRole()) {
            $primerUsuario->assignRole('admin');
        }

        // Asignar rol de user a todos los demás usuarios existentes
        $primerUsuarioId = $primerUsuario ? $primerUsuario->id : 0;
        $otrosUsuarios = User::where('id', '!=', $primerUsuarioId)->get();
        foreach ($otrosUsuarios as $usuario) {
            if (!$usuario->hasAnyRole()) {
                $usuario->assignRole('user');
            }
        }
    }
}
