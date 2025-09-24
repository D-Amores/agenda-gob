<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TelegramBotInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:bot-info {--test-message= : Enviar mensaje de prueba a este Chat ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra información del bot de Telegram y permite enviar mensajes de prueba';

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
        $this->info('🤖 Información del Bot de Telegram');
        $this->info('================================');

        // Verificar configuración
        $botToken = config('telegram.bot_token');
        if (!$botToken) {
            $this->error('❌ Token del bot no configurado en el archivo .env');
            $this->info('💡 Agrega TELEGRAM_BOT_TOKEN=tu_token en tu archivo .env');
            return 1;
        }

        $this->info('✅ Token del bot configurado');

        // Obtener información del bot
        $botInfo = $this->telegramService->getBotInfo();

        if ($botInfo) {
            $this->info("🤖 Nombre del bot: {$botInfo['first_name']}");
            $this->info("👤 Username: @{$botInfo['username']}");
            $this->info("🆔 Bot ID: {$botInfo['id']}");
            $this->info("✅ Estado: Activo y funcionando");
        } else {
            $this->error('❌ No se pudo obtener información del bot. Verifica el token.');
            return 1;
        }

        // Mostrar configuración
        $this->info("\n⚙️ Configuración actual:");
        $this->info("🕐 Hora de notificaciones: " . config('telegram.notifications.time', '08:00'));
        $this->info("🌍 Zona horaria: " . config('telegram.notifications.timezone', 'America/Mexico_City'));
        $this->info("📱 Notificaciones habilitadas: " . (config('telegram.notifications.enabled') ? 'Sí' : 'No'));

        // Enviar mensaje de prueba si se especifica
        $testChatId = $this->option('test-message');
        if ($testChatId) {
            $this->info("\n🧪 Enviando mensaje de prueba...");
            $success = $this->telegramService->sendTestMessage($testChatId);

            if ($success) {
                $this->info("✅ Mensaje de prueba enviado exitosamente al chat ID: {$testChatId}");
            } else {
                $this->error("❌ Error enviando mensaje de prueba. Verifica el Chat ID.");
                return 1;
            }
        }

        $this->info("\n💡 Para obtener tu Chat ID:");
        $this->info("1. Envía un mensaje a @{$botInfo['username']} en Telegram");
        $this->info("2. Visita: https://api.telegram.org/bot{$botToken}/getUpdates");
        $this->info("3. Busca tu 'chat.id' en la respuesta JSON");

        $this->info("\n🔧 Comandos útiles:");
        $this->info("• php artisan telegram:send-daily-notifications --test");
        $this->info("• php artisan telegram:bot-info --test-message=CHAT_ID");

        return 0;
    }
}
