<?php

namespace App\Services;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception as TelegramException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Models\Audiencia;
use App\Models\Evento;
use Carbon\Carbon;

class TelegramService
{
    protected $telegram;
    protected $botToken;
    protected $defaultChatId;

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token');
        $this->defaultChatId = config('telegram.default_chat_id');

        if ($this->botToken) {
            $this->telegram = new BotApi($this->botToken);
            
            // Configurar cURL para manejar certificados SSL en desarrollo/local
            $this->configureHttpClient();
        }
    }

    /**
     * Configura el cliente HTTP para manejar certificados SSL
     */
    private function configureHttpClient(): void
    {
        // Configurar opciones de cURL para resolver problemas de SSL
        $curlOptions = [
            CURLOPT_SSL_VERIFYPEER => false,  // Desactivar verificación de certificados SSL
            CURLOPT_SSL_VERIFYHOST => false,  // Desactivar verificación del host SSL
            CURLOPT_TIMEOUT => 30,            // Timeout de 30 segundos
            CURLOPT_CONNECTTIMEOUT => 10,     // Timeout de conexión de 10 segundos
        ];

        // Solo aplicar estas configuraciones en desarrollo local
        if (app()->environment(['local', 'development'])) {
            // Crear un cliente HTTP personalizado
            $httpClient = new \GuzzleHttp\Client([
                'verify' => false,  // Desactivar verificación SSL en desarrollo
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
            
            // Configurar el cliente en el bot de Telegram si es posible
            try {
                // La librería telegram-bot/api usa cURL internamente,
                // configuramos las opciones a nivel global
                curl_setopt_array(curl_init(), $curlOptions);
            } catch (\Throwable $e) {
                Log::warning("Could not configure cURL options: " . $e->getMessage());
            }
        }
    }

    /**
     * Envía un mensaje a un chat específico
     */
    public function sendMessage(string $chatId, string $message, array $options = []): bool
    {
        if (!$this->telegram) {
            Log::error('Telegram bot token not configured');
            return false;
        }

        try {
            // Configurar opciones de cURL antes de la petición
            if (app()->environment(['local', 'development'])) {
                $this->configureCurlForDevelopment();
            }

            // Agregar parse_mode por defecto para soportar Markdown (versión 1, más simple)
            $defaultOptions = [
                'parse_mode' => 'Markdown'
            ];

            // Combinar opciones por defecto con las opciones personalizadas
            $finalOptions = array_merge($defaultOptions, $options);

            $this->telegram->sendMessage($chatId, $message, $finalOptions['parse_mode'], false, null, null, $finalOptions);
            Log::info("Telegram message sent to chat: {$chatId}");
            return true;
        } catch (TelegramException $e) {
            Log::error("Failed to send Telegram message: " . $e->getMessage());
            
            // Intentar envío alternativo si es problema de SSL
            if (strpos($e->getMessage(), 'SSL certificate') !== false || 
                strpos($e->getMessage(), 'certificate') !== false) {
                Log::info("Attempting alternative SSL configuration for Telegram message");
                return $this->sendMessageWithAlternativeSSL($chatId, $message, $options);
            }
            
            return false;
        }
    }

    /**
     * Configura cURL para desarrollo local
     */
    private function configureCurlForDevelopment(): void
    {
        // Configurar opciones globales de cURL para desarrollo
        curl_setopt_array(curl_init(), [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
    }

    /**
     * Método alternativo para enviar mensajes con configuración SSL más permisiva
     */
    private function sendMessageWithAlternativeSSL(string $chatId, string $message, array $options = []): bool
    {
        try {
            // Usar cURL directo para mayor control sobre SSL
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            
            $postData = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error("Alternative cURL error: " . $error);
                return false;
            }

            if ($httpCode === 200) {
                Log::info("Telegram message sent successfully via alternative method to chat: {$chatId}");
                return true;
            } else {
                Log::error("Alternative method HTTP error: {$httpCode}, Response: {$response}");
                return false;
            }

        } catch (\Throwable $e) {
            Log::error("Alternative SSL method failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía notificaciones diarias a todos los usuarios con eventos/audiencias
     */
    public function sendDailyNotifications(): array
    {
        $results = [];
        $today = Carbon::now()->format('Y-m-d');

        // Obtener usuarios que tienen eventos o audiencias hoy
        $usersWithEvents = $this->getUsersWithTodayEvents($today);
        $usersWithAudiencias = $this->getUsersWithTodayAudiencias($today);

        // Combinar usuarios únicos
        $allUsers = collect($usersWithEvents)->merge($usersWithAudiencias)->unique('id');

        foreach ($allUsers as $user) {
            if ($user->telegram_chat_id && $user->telegram_notifications_enabled) {
                $message = $this->buildDailyMessage($user, $today);
                $success = $this->sendMessage($user->telegram_chat_id, $message);

                $results[] = [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'chat_id' => $user->telegram_chat_id,
                    'success' => $success
                ];
            }
        }

        return $results;
    }

    /**
     * Construye el mensaje diario para un usuario
     */
    protected function buildDailyMessage(User $user, string $date): string
    {
        $audiencias = Audiencia::where('user_id', $user->id)
            ->whereDate('fecha_audiencia', $date)
            ->with(['estatus', 'area'])
            ->orderBy('hora_audiencia')
            ->get();

        $eventos = Evento::where('user_id', $user->id)
            ->whereDate('fecha_evento', $date)
            ->with(['estatus', 'area'])
            ->orderBy('hora_evento')
            ->get();

        $message = "✨ *AGENDA DEL DÍA " . Carbon::parse($date)->format('d/m/Y') . "*\n";
        $message .= "👋 Hola *{$user->name}*\n\n";

        if ($audiencias->count() > 0) {
            $message .= "━━━━━━━━━━━━━━━━━\n";
            $message .= "  🟢 *AUDIENCIAS (" . $audiencias->count() . ")*\n";
            $message .= "━━━━━━━━━━━━━━━━━\n\n";

            foreach ($audiencias as $index => $audiencia) {
                $message .= "*{$audiencia->nombre}*\n\n";
                $message .= "   🕐 - *Horario:* `{$audiencia->hora_audiencia}` - `{$audiencia->hora_fin_audiencia}`\n";
                $message .= "   📍 - *Lugar:* `{$audiencia->lugar}`\n";
                $message .= "   📝 - *Asunto:* " . substr($audiencia->asunto_audiencia, 0, 60) .
                        (strlen($audiencia->asunto_audiencia) > 60 ? "..." : "") . "\n";
                $message .= "   📊 - *Estado:* `" . ($audiencia->estatus->estatus ?? 'N/A') . "`\n";

                if ($audiencia->area) {
                    $message .= "   🏢 - *Área:* `{$audiencia->area->area}`\n";
                }

                // Separador entre audiencias (excepto la última)
                if ($index < $audiencias->count() - 1) {
                    $message .= "\n   ────────────────\n\n";
                }
            }
            $message .= "\n";
        }

        if ($eventos->count() > 0) {
            // Espacio antes de la sección de eventos
            if ($audiencias->count() > 0) {
                $message .= "\n";
            }

            $message .= "━━━━━━━━━━━━━━━━━\n";
            $message .= "  🟠 *EVENTOS (" . $eventos->count() . ")*\n";
            $message .= "━━━━━━━━━━━━━━━━━\n\n";

            foreach ($eventos as $index => $evento) {
                $message .= "*{$evento->nombre}*\n\n";
                $message .= "   🕐 - *Horario:* `{$evento->hora_evento}` - `{$evento->hora_fin_evento}`\n";
                $message .= "   📍 - *Lugar:* `{$evento->lugar}`\n";

                if ($evento->descripcion) {
                    $message .= "   📝 - *Descripción:* " . substr($evento->descripcion, 0, 60) .
                            (strlen($evento->descripcion) > 60 ? "..." : "") . "\n";
                }

                $message .= "   📊 - *Estado:* `" . ($evento->estatus->estatus ?? 'N/A') . "`\n";

                if ($evento->area) {
                    $message .= "   🏢 - *Área:* `{$evento->area->area}`\n";
                }

                if ($evento->asistencia_de_gobernador) {
                    $message .= "   👨‍💼 - *Asistencia del Gobernador:* Confirmada\n";
                } else {
                    $message .= "   👨‍💼 - *Asistencia del Gobernador:* No Confirmada\n";
                }

                // Separador entre eventos (excepto el último)
                if ($index < $eventos->count() - 1) {
                    $message .= "\n   ────────────────\n\n";
                }
            }
            $message .= "\n";
        }

        if ($audiencias->count() === 0 && $eventos->count() === 0) {
            $message .= "📅 - *No tienes actividades programadas para hoy*\n\n";
            $message .= "🌞 - ¡Que tengas un excelente día! 😊";
        } else {
            $message .= "━━━━━━━━━━━━━━━━━\n";
            $message .= "*RESUMEN DEL DÍA*\n";
            $message .= "   🟢 - *Audiencias:* {$audiencias->count()}\n";
            $message .= "   🟠 - *Eventos:* {$eventos->count()}\n";
            $message .= "   - *Total:* " . ($audiencias->count() + $eventos->count()) . " actividades\n\n";
            $message .= "💪 ¡Que tengas un día muy productivo!";
        }

        return $message;
    }

    /**
     * Obtiene usuarios con eventos para hoy
     */
    protected function getUsersWithTodayEvents(string $date)
    {
        return User::whereHas('eventos', function ($query) use ($date) {
            $query->whereDate('fecha_evento', $date);
        })->get();
    }

    /**
     * Obtiene usuarios con audiencias para hoy
     */
    protected function getUsersWithTodayAudiencias(string $date)
    {
        return User::whereHas('audiencias', function ($query) use ($date) {
            $query->whereDate('fecha_audiencia', $date);
        })->get();
    }

    /**
     * Envía un mensaje de prueba
     */
    public function sendTestMessage(string $chatId): bool
    {
        $message = "🤖 ¡Hola! Este es un mensaje de prueba del bot de Agenda Gubernamental.\n\n";
        $message .= "✅ La configuración está funcionando correctamente.\n";
        $message .= "📅 Recibirás notificaciones diarias sobre tus eventos y audiencias.";

        return $this->sendMessage($chatId, $message);
    }

    /**
     * Configura el webhook para el bot
     */
    public function setWebhook(string $url): bool
    {
        if (!$this->telegram) {
            return false;
        }

        try {
            $this->telegram->setWebhook($url);
            Log::info("Telegram webhook set to: {$url}");
            return true;
        } catch (TelegramException $e) {
            Log::error("Failed to set Telegram webhook: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene información del bot
     */
    public function getBotInfo(): ?array
    {
        if (!$this->telegram) {
            return null;
        }

        try {
            $me = $this->telegram->getMe();
            return [
                'id' => $me->getId(),
                'username' => $me->getUsername(),
                'first_name' => $me->getFirstName(),
                'is_bot' => $me->isBot()
            ];
        } catch (TelegramException $e) {
            Log::error("Failed to get bot info: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene actualizaciones recientes del bot
     */
    public function getRecentUpdates(int $offset = 0, int $limit = 10): array
    {
        if (!$this->telegram) {
            return [];
        }

        try {
            $updates = $this->telegram->getUpdates($offset, $limit);
            return $updates;
        } catch (TelegramException $e) {
            Log::error("Failed to get recent updates: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Envía notificación de nueva audiencia registrada
     */
    public function sendAudienciaRegistradaNotification(Audiencia $audiencia, User $user): bool
    {
        if (!$user->telegram_chat_id) {
            Log::warning("User {$user->id} does not have telegram_chat_id configured");
            return false;
        }

        $fechaFormateada = Carbon::parse($audiencia->fecha_audiencia)->format('d/m/Y');
        
        $message = "✅ *AUDIENCIA REGISTRADA*\n\n";
        $message .= "🎉 ¡Tu audiencia ha sido registrada exitosamente!\n\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 **DETALLES DE LA AUDIENCIA**\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        $message .= "👤 **Nombre:** `{$audiencia->nombre}`\n\n";
        $message .= "📝 **Asunto:** " . substr($audiencia->asunto_audiencia, 0, 100) . 
                   (strlen($audiencia->asunto_audiencia) > 100 ? "..." : "") . "\n\n";
        $message .= "📅 **Fecha:** `{$fechaFormateada}`\n\n";
        $message .= "🕐 **Horario:** `{$audiencia->hora_audiencia}` - `{$audiencia->hora_fin_audiencia}`\n\n";
        $message .= "📍 **Lugar:** `{$audiencia->lugar}`\n\n";
        
        if ($audiencia->procedencia) {
            $message .= "🏢 **Procedencia:** `{$audiencia->procedencia}`\n\n";
        }
        
        if ($audiencia->area) {
            $message .= "🏛️ **Área:** `{$audiencia->area->area}`\n\n";
        }
        
        $message .= "📊 **Estado:** `" . ($audiencia->estatus->estatus ?? 'Programado') . "`\n\n";
        
        if ($audiencia->descripcion) {
            $message .= "📄 **Descripción:** " . substr($audiencia->descripcion, 0, 150) . 
                       (strlen($audiencia->descripcion) > 150 ? "..." : "") . "\n\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "🔔 **Recordatorio:** Recibirás notificaciones diarias sobre tus audiencias programadas.\n\n";
        $message .= "💼 ¡Que tengas una audiencia exitosa!";

        return $this->sendMessage($user->telegram_chat_id, $message);
    }

    public function sendEventoRegistradoNotification(Evento $evento, User $user): bool
    {
        if (!$user->telegram_chat_id) {
            Log::warning("User {$user->id} does not have telegram_chat_id configured");
            return false;
        }

        $fechaFormateada = Carbon::parse($evento->fecha_evento)->format('d/m/Y');

        $message = "✅ *EVENTO REGISTRADO*\n\n";
        $message .= "🎉 ¡Tu evento ha sido registrado exitosamente!\n\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 **DETALLES DEL EVENTO**\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        $message .= "👤 **Nombre:** `{$evento->nombre}`\n\n";
        $message .= "📅 **Fecha:** `{$fechaFormateada}`\n\n";
        $message .= "🕐 **Horario:** `{$evento->hora_evento}` - `{$evento->hora_fin_evento}`\n\n";
        $message .= "📍 **Lugar:** `{$evento->lugar}`\n\n";

        if ($evento->descripcion) {
            $message .= "� **Descripción:** " . substr($evento->descripcion, 0, 150) .
                   (strlen($evento->descripcion) > 150 ? "..." : "") . "\n\n";
        }

        if ($evento->area) {
            $message .= "🏛️ **Área:** `{$evento->area->area}`\n\n";
        }

        $message .= "📊 **Estado:** `" . ($evento->estatus->estatus ?? 'Programado') . "`\n\n";

        if ($evento->asistencia_de_gobernador) {
            $message .= "👨‍💼 **Asistencia del Gobernador:** Confirmada\n\n";
        } else {
            $message .= "👨‍💼 **Asistencia del Gobernador:** No Confirmada\n\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "🔔 **Recordatorio:** Recibirás notificaciones diarias sobre tus eventos programados.\n\n";
        $message .= "🎊 ¡Que tengas un evento exitoso!";

        return $this->sendMessage($user->telegram_chat_id, $message);
    }
}
