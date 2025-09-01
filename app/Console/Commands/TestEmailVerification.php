<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class TestEmailVerification extends Command
{
    protected $signature = 'test:email-verification';
    protected $description = 'Test email verification functionality';

    public function handle()
    {
        $this->info('Testing email verification functionality...');
        
        // Verificar configuración de mail
        $mailDriver = config('mail.default');
        $this->info("Mail driver: {$mailDriver}");
        
        if ($mailDriver === 'log') {
            $this->warn('Using log driver - emails will be saved to storage/logs/laravel.log');
        }
        
        // Verificar usuarios no verificados
        $unverifiedUsers = User::whereNull('email_verified_at')->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        
        $this->info("Users with unverified email: {$unverifiedUsers}");
        $this->info("Users with verified email: {$verifiedUsers}");
        
        // Test de rutas
        $routes = [
            'verification.notice',
            'verification.verify', 
            'verification.send'
        ];
        
        $this->info('Available verification routes:');
        foreach ($routes as $route) {
            if (route($route, ['id' => 1, 'hash' => 'test'], false)) {
                $this->line("✅ {$route}");
            } else {
                $this->error("❌ {$route}");
            }
        }
        
        $this->info('✅ Email verification system is configured!');
        $this->line('Register a new user to test the email verification flow.');
    }
}
