<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Comando para cambiar estatus de eventos/audiencias vencidos
        $schedule->command('eventos:cambiar-estatus-vencidos')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Comando para enviar notificaciones diarias por Telegram
        $schedule->command('telegram:send-daily-notifications')
                 ->dailyAt('01:08') // Cambiado temporalmente para prueba
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->timezone(config('telegram.notifications.timezone', 'America/Mexico_City'));

        // Bot responder automático - procesa mensajes cada minuto
        $schedule->command('telegram:bot-responder')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
