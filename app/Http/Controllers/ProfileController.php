<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (!$user) {
            abort(404);
        }

        if ($user->id !== auth()->id()) {
            return redirect()->route('profile.edit', auth()->user());
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProfileRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request, User $user)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        if ($user->id !== auth()->id()) {
            $response['message'] = 'No autorizado.';
            return response()->json($response, 403);
        }

        try {
            $data = $request->only(['username']);

            if ($request->hasFile('profile_photo')) {
                // elimina la foto anterior si existe
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $request->file('profile_photo')->store('profiles', 'public');
                $data['profile_photo_path'] = $path;
            }
            $user->update($data);
            $response['ok'] = true;
            $response['message'] = 'Perfil actualizado correctamente.';
            return response()->json($response);
        } catch (\Throwable $e) {
            report($e);
            $response['message'] = 'No se pudo actualizar el perfil.';
            return response()->json($response, 500);
        }
    }

    /**
     * Cambiar contraseña del usuario
     *
     * @param  \App\Http\Requests\ChangePasswordRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request, User $user)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        // Verificar que el usuario solo pueda cambiar su propia contraseña
        if ($user->id !== auth()->id()) {

            $response['message'] = 'No tienes permisos para cambiar esta contraseña.';
            return response()->json($response, 403);
        }

        try {
            // Actualizar la contraseña
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            $response['ok'] = true;
            $response['message'] = 'Contraseña cambiada exitosamente.';
            return response()->json($response, 200);

        } catch (\Exception $e) {
            report($e);
            $response['message'] = 'Error al cambiar la contraseña. Por favor, inténtalo de nuevo.';
            return response()->json($response, 500);
        }
    }

    /**
     * Configurar Telegram para el usuario
     */
    public function configureTelegram(User $user)
    {
        $this->authorize('update', $user);

        $botUsername = config('telegram.bot_username');
        $botInfo = null;

        // Intentar obtener información del bot
        try {
            $telegramService = app(\App\Services\TelegramService::class);
            $botInfo = $telegramService->getBotInfo();
        } catch (\Exception $e) {
            // Si no se puede obtener info del bot, usar valores por defecto
        }

        return view('profile.telegram', compact('user', 'botUsername', 'botInfo'));
    }

    /**
     * Actualizar configuración de Telegram
     */
    public function updateTelegram(Request $request, User $user)
    {
        // Logs básicos
        Log::info('=== INICIO updateTelegram ===');
        Log::info('Request data:', $request->all());
        Log::info('User ID:', ['id' => $user->id]);

        // Validación simple
        $chatId = $request->get('telegram_chat_id');
        $notificationsEnabled = $request->has('telegram_notifications_enabled');

        Log::info('Datos procesados:', [
            'chat_id' => $chatId,
            'notifications' => $notificationsEnabled
        ]);

        // Actualización directa
        $result = $user->update([
            'telegram_chat_id' => $chatId,
            'telegram_notifications_enabled' => $notificationsEnabled,
        ]);

        Log::info('Resultado update:', ['success' => $result]);
        Log::info('Usuario después update:', [
            'telegram_chat_id' => $user->fresh()->telegram_chat_id,
            'telegram_notifications_enabled' => $user->fresh()->telegram_notifications_enabled,
        ]);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }

    /**
     * Detectar Chat ID automáticamente desde Telegram
     */
    public function detectChatId(Request $request, User $user)
    {
        // Verificar que el usuario autenticado pueda editar este perfil
        if ($user->id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        try {
            $telegramService = app(\App\Services\TelegramService::class);

            // Obtener actualizaciones recientes del bot
            $updates = $telegramService->getRecentUpdates();

            if (!$updates) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron obtener las actualizaciones del bot.'
                ]);
            }

            // Buscar mensajes recientes (últimos 10 minutos)
            $recentChatIds = [];
            $tenMinutesAgo = time() - 600; // 10 minutos

            foreach ($updates as $update) {
                $message = $update->getMessage();

                if ($message) {
                    $messageTime = $message->getDate();

                    if ($messageTime >= $tenMinutesAgo) {
                        $chat = $message->getChat();
                        $from = $message->getFrom();

                        $chatId = $chat->getId();
                        $firstName = $from->getFirstName() ?? '';
                        $lastName = $from->getLastName() ?? '';
                        $username = $from->getUsername() ?? '';

                        $recentChatIds[] = [
                            'chat_id' => $chatId,
                            'name' => trim($firstName . ' ' . $lastName),
                            'username' => $username,
                            'message' => $message->getText() ?? '',
                            'time' => date('H:i:s', $messageTime)
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'chat_ids' => $recentChatIds,
                'message' => count($recentChatIds) > 0
                    ? 'Se encontraron ' . count($recentChatIds) . ' mensajes recientes.'
                    : 'No se encontraron mensajes recientes. Envía un mensaje al bot primero.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al detectar Chat ID: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar mensaje de prueba de Telegram
     */
    public function testTelegram(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        $validated = $request->validate([
            'chat_id' => 'required|string'
        ]);

        try {
            $telegramService = app(\App\Services\TelegramService::class);
            $success = $telegramService->sendTestMessage($validated['chat_id']);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Mensaje enviado exitosamente' : 'No se pudo enviar el mensaje'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
