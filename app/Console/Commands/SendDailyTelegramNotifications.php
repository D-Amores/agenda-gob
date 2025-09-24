<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use Carbon\Carbon;

class SendDailyTelegramNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:send-daily-notifications
                            {--test : Enviar solo mensaje de prueba}
                            {--chat-id= : Chat ID específico para pruebas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones diarias por Telegram sobre eventos y audiencias del día';

    protected $telegramService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🤖 Iniciando envío de notificaciones diarias por Telegram...');

        // Si es modo de prueba
        if ($this->option('test')) {
            return $this->handleTestMode();
        }

        // Verificar configuración
        if (!config('telegram.bot_token')) {
            $this->error('❌ Token de bot de Telegram no configurado. Revisa tu archivo .env');
            return 1;
        }

        if (!config('telegram.notifications.enabled')) {
            $this->info('📵 Las notificaciones de Telegram están deshabilitadas');
            return 0;
        }

        // Enviar notificaciones
        $results = $this->telegramService->sendDailyNotifications();

        if (empty($results)) {
            $this->info('📅 No hay usuarios con eventos o audiencias para hoy');
            return 0;
        }

        // Mostrar resultados
        $successful = 0;
        $failed = 0;

        foreach ($results as $result) {
            if ($result['success']) {
                $successful++;
                $this->line("✅ Notificación enviada a {$result['username']} (ID: {$result['user_id']})");
            } else {
                $failed++;
                $this->line("❌ Error enviando a {$result['username']} (ID: {$result['user_id']})");
            }
        }

        $this->info("\n📊 Resumen:");
        $this->info("✅ Notificaciones enviadas exitosamente: {$successful}");
        $this->info("❌ Notificaciones fallidas: {$failed}");
        $this->info("📧 Total de usuarios procesados: " . count($results));

        return 0;
    }

    /**
     * Maneja el modo de prueba
     */
    protected function handleTestMode(): int
    {
        $chatId = $this->option('chat-id');

        if (!$chatId) {
            $chatId = $this->ask('Ingresa el Chat ID para enviar el mensaje de prueba:');
        }

        if (!$chatId) {
            $this->error('❌ Chat ID es requerido para el modo de prueba');
            return 1;
        }

        $this->info("🧪 Enviando mensaje de prueba al chat ID: {$chatId}");

        $success = $this->telegramService->sendTestMessage($chatId);

        if ($success) {
            $this->info('✅ Mensaje de prueba enviado exitosamente');
            return 0;
        } else {
            $this->error('❌ Error enviando mensaje de prueba. Verifica el Chat ID y la configuración del bot');
            return 1;
        }
    }
}
