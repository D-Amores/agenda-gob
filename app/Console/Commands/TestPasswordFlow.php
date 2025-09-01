<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Facades\Hash;
use App\Notifications\PasswordGenerated;

class TestPasswordFlow extends Command
{
    protected $signature = 'test:password-flow {email}';
    protected $description = 'Probar el flujo de generación y envío de contraseña';

    public function handle()
    {
        $email = $this->argument('email');
        
        // Buscar el usuario
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            return;
        }
        
        // Generar una contraseña de prueba
        $password = $this->generateSecurePassword();
        
        $this->info("Enviando contraseña de prueba a: {$user->email}");
        $this->info("Usuario: {$user->username}");
        $this->info("Contraseña generada: {$password}");
        
        // Enviar la notificación
        $user->notify(new PasswordGenerated($password));
        
        $this->info("✅ Email enviado exitosamente!");
        $this->info("Revisa la bandeja de entrada de: {$email}");
    }
    
    private function generateSecurePassword($length = 12)
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '@$!%*?&';
        
        // Asegurar que tenga al menos uno de cada tipo
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Llenar el resto con caracteres aleatorios
        $allChars = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mezclar los caracteres
        return str_shuffle($password);
    }
}
