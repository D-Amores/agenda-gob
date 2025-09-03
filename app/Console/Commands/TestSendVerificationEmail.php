<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class TestSendVerificationEmail extends Command
{
    protected $signature = 'test:send-verification-email {user_id?}';
    protected $description = 'Test sending verification email to a specific user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            // Buscar un usuario sin verificar
            $user = User::whereNull('email_verified_at')->first();
            if (!$user) {
                $this->error('No hay usuarios sin verificar en la base de datos.');
                return;
            }
        } else {
            $user = User::find($userId);
            if (!$user) {
                $this->error("Usuario con ID {$userId} no encontrado.");
                return;
            }
        }

        $this->info("Enviando email de verificación a: {$user->email}");
        $this->info("Usuario: {$user->username}");
        
        try {
            // Enviar email de verificación
            $user->sendEmailVerificationNotification();
            
            $this->info('✅ Email de verificación enviado exitosamente!');
            $this->line('Revisa el archivo: storage/logs/laravel.log');
            $this->line('Busca el enlace de verificación en el log.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar email: ' . $e->getMessage());
        }
    }
}
