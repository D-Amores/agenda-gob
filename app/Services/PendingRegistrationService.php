<?php

namespace App\Services;

use App\Models\PendingRegistration;
use App\Models\User;
use App\Notifications\PasswordDeliveryNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Tools\Tools;

class PendingRegistrationService
{
    public function confirm(PendingRegistration $pendingRegistration): User
    {
        // Crear el usuario definitivo
        $user = User::create([
            'name' => $pendingRegistration->name,
            'username' => $pendingRegistration->username,
            'email' => $pendingRegistration->email,
            'phone' => $pendingRegistration->phone,
            'password' => $pendingRegistration->password, // ya hasheada
            'area_id' => $pendingRegistration->area_id,
            'email_verified_at' => now(),
        ]);
        // Asignar rol automáticamente
        $roleToAssign = $pendingRegistration->rol ?: 'user';
        $user->assignRole($roleToAssign);


        // Verificar que el email se marcó como verificado
        $user->markEmailAsVerified();

        Log::info('Usuario creado', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'verified'=> $user->hasVerifiedEmail(),
        ]);

        // Generar una nueva contraseña para enviar al usuario
        $newPassword = Tools::generateSecurePassword();
        $user->update(['password' => Hash::make($newPassword)]);

        // Notificar por correo
        try{
            $user->notify(new PasswordDeliveryNotification($newPassword));
        }catch(\Throwable $e){
            Log::error('Error al enviar la notificación de contraseña: ' . $e->getMessage());
        }

        // Eliminar el registro pendiente
        $pendingRegistration->delete();

        // Limpieza de registros expirados
        PendingRegistration::expired()->delete();

        return $user;
    }
}
