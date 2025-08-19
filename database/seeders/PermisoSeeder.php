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

        // Permisos comunes para eventos y audiencias
        $acciones = ['ver', 'crear', 'editar', 'eliminar'];
        $modulos = ['evento', 'audiencia'];

        $permisos = [];

        foreach ($modulos as $modulo) {
            foreach ($acciones as $accion) {
                $permisos[] = "$accion $modulo";
            }
        }

        // Crear permisos si no existen
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $encargado = Role::firstOrCreate(['name' => 'encargado']);
        $usuario = Role::firstOrCreate(['name' => 'usuario']);

        // Asignar todos los permisos al admin
        $admin->syncPermissions(Permission::all());

        // Encargado: puede ver, crear, y editar, pero no eliminar
        $permisosEncargado = array_filter($permisos, function ($p) {
            return !str_starts_with($p, 'eliminar');
        });
        $encargado->syncPermissions($permisosEncargado);

        // Usuario: solo puede ver y crear
        $permisosUsuario = array_filter($permisos, function ($p) {
            return str_starts_with($p, 'ver') || str_starts_with($p, 'crear');
        });
        $usuario->syncPermissions(Permission::all());

        // (Opcional) Asignar rol a un usuario existente
        // Asignar rol de admin a los dos primeros usuarios (si existen)
        $admins = User::take(2)->get();

        foreach ($admins as $adminUser) {
            if (!$adminUser->hasRole('usuario')) {
                $adminUser->assignRole('usuario');
            }
        }

    }
}
