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
}
