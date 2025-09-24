<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use TelegramBot\Api\Types\Update;

class TelegramBotResponder extends Command
{
    protected $signature = 'telegram:bot-responder {--watch : Watch for new messages continuously}';
    protected $description = 'Responde automáticamente a mensajes del bot de Telegram';

    public function handle()
    {
        $telegramService = new TelegramService();

        if ($this->option('watch')) {
            $this->info('🤖 Iniciando modo vigilancia del bot...');
            $this->info('Presiona Ctrl+C para detener');

            $lastUpdateId = 0;

            while (true) {
                try {
                    // Obtener solo actualizaciones nuevas
                    $updates = $telegramService->getRecentUpdates($lastUpdateId + 1);

                    foreach ($updates as $update) {
                        $lastUpdateId = max($lastUpdateId, $update->getUpdateId());
                        $this->processUpdate($update, $telegramService);
                    }

                    // Esperar 2 segundos antes de la siguiente consulta
                    sleep(2);

                } catch (\Exception $e) {
                    $this->error('Error: ' . $e->getMessage());
                    sleep(5);
                }
            }
        } else {
            $this->info('Iniciando bot responder...');

            // Obtener el último update ID procesado
            $lastProcessedId = cache()->get('telegram_last_update_id', 0);

            // Obtener solo actualizaciones nuevas
            $updates = $telegramService->getRecentUpdates($lastProcessedId + 1);

            if (count($updates) > 0) {
                $this->info('Procesando ' . count($updates) . ' mensajes nuevos...');

                foreach ($updates as $update) {
                    $this->processUpdate($update, $telegramService);
                    // Actualizar el último ID procesado
                    cache()->put('telegram_last_update_id', $update->getUpdateId(), now()->addDays(7));
                }
            } else {
                $this->info('No hay mensajes nuevos para procesar.');
            }

            $this->info('Bot responder completado.');
        }
    }

    private function processUpdate(Update $update, TelegramService $telegramService)
    {
        $message = $update->getMessage();

        if (!$message) {
            return;
        }

        $chatId = $message->getChat()->getId();
        $text = $message->getText();
        $userName = $message->getFrom()->getFirstName() . ' ' . $message->getFrom()->getLastName();

        $this->info("Mensaje de {$userName} ({$chatId}): {$text}");

        // Responder según el mensaje
        switch (strtolower(trim($text))) {
            case '/start':
                $response = "¡Hola {$userName}! 👋\n\n";
                $response .= "Bienvenido al bot de Agenda Personal.\n\n";
                $response .= "Tu Chat ID es: `{$chatId}`\n\n";
                $response .= "Para recibir notificaciones diarias:\n";
                $response .= "1. Copia tu Chat ID: {$chatId}\n";
                $response .= "2. Ve a tu perfil en el sistema\n";
                $response .= "3. Pégalo en la configuración de Telegram\n";
                $response .= "4. ¡Listo! Recibirás tus eventos diarios a las 8:00 AM\n\n";
                $response .= "Comandos disponibles:\n";
                $response .= "/help - Ver esta ayuda\n";
                $response .= "/chatid - Ver tu Chat ID\n";
                $response .= "/test - Probar notificaciones";
                break;

            case '/help':
                $response = "🤖 **Ayuda del Bot de Agenda**\n\n";
                $response .= "Comandos disponibles:\n";
                $response .= "/start - Inicio y configuración\n";
                $response .= "/chatid - Ver tu Chat ID: `{$chatId}`\n";
                $response .= "/test - Probar notificaciones\n";
                $response .= "/help - Esta ayuda\n\n";
                $response .= "ℹ️ Tu Chat ID es: `{$chatId}`";
                break;

            case '/chatid':
                $response = "🆔 **Tu Chat ID es:**\n\n`{$chatId}`\n\nCopia este número para configurar las notificaciones en tu perfil.";
                break;

            case '/test':
                $response = "🧪 **Probando notificaciones...**\n\n";
                $response .= "Este es un mensaje de prueba del bot de Agenda Personal.\n\n";
                $response .= "Si recibes este mensaje, ¡la configuración está correcta!\n\n";
                $response .= "Tu Chat ID: `{$chatId}`";
                break;

            default:
                $response = "Hola {$userName}! 👋\n\n";
                $response .= "No entiendo ese comando. Usa /help para ver los comandos disponibles.\n\n";
                $response .= "Tu Chat ID es: `{$chatId}`";
                break;
        }

        // Enviar respuesta
        $sent = $telegramService->sendMessage($chatId, $response);

        if ($sent) {
            $this->info("✅ Respuesta enviada a {$userName}");
        } else {
            $this->error("❌ Error enviando respuesta a {$userName}");
        }
    }
}
