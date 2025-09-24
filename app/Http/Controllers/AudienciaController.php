<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estatus;
use App\Models\Audiencia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\TelegramService;

class AudienciaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Audiencia::class, 'audiencia');
    }
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
    public function create()
    {
        $estatus = Estatus::statusProgramado();
        return view('audiencia.registro', compact('estatus'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TelegramService $telegramService)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        $status = 422;
        // Validación manual para poder responder 422 en JSON uniformemente
        $validator = Validator::make($request->all(), [
            'formValidationName'   => 'required|string|min:10|max:255',
            'formValidationAsunto' => 'required|string|min:10|max:255',
            'formValidationLugar'  => 'required|string|min:10|max:255',
            'formValidationFecha'  => 'required|date',
            'procedencia'          => 'nullable|string|max:255',
            'hora_audiencia'       => 'required|date_format:H:i',
            'hora_fin_audiencia'   => 'required|date_format:H:i|after:hora_audiencia',
            //'estatus_id'           => 'required|exists:estatus,id',
            'descripcion'          => 'nullable|string|max:500',
            'notificar_telegram'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $response['message'] = 'Errores de validación.';
            $response['errors'] = $validator->errors();
            return response()->json($response, $status);
        }

        $validated = $validator->validated();

        // Normalizar fecha
        try {
            $validated['formValidationFecha'] = Carbon::parse($validated['formValidationFecha'])->format('Y-m-d');
        } catch (\Throwable $e) {
            $response['message'] = 'Fecha inválida.';
            return response()->json($response, $status);
        }

        if (Audiencia::isAudienciaDuplicated($validated, Auth::user()->area_id)) {
            $response['message'] = 'Ya existe una audiencia con ese nombre en esa fecha y hora.';
            return response()->json($response, $status);
        }

        try {
            $audiencia = Audiencia::create([
                'nombre'            => $validated['formValidationName'],
                'asunto_audiencia'  => $validated['formValidationAsunto'],
                'lugar'             => $validated['formValidationLugar'],
                'fecha_audiencia'   => $validated['formValidationFecha'],
                'procedencia'       => $validated['procedencia'] ?? null,
                'hora_audiencia'    => $validated['hora_audiencia'],
                'hora_fin_audiencia' => $validated['hora_fin_audiencia'],
                'area_id'           => Auth::user()->area_id,
                'estatus_id'        => Estatus::statusProgramado()->id,
                'descripcion'       => $validated['descripcion'] ?? null,
                'user_id'           => Auth::id(),
            ]);

            // Enviar notificación por Telegram si está habilitada
            if (isset($validated['notificar_telegram']) && $validated['notificar_telegram']) {
                $user = Auth::user();
                
                // Verificar que el usuario tenga configurado su chat_id de Telegram
                if ($user->telegram_chat_id) {
                    
                    // Cargar las relaciones necesarias para la notificación
                    $audiencia->load(['estatus', 'area']);
                    
                    $notificationSent = $telegramService->sendAudienciaRegistradaNotification($audiencia, $user);
                    
                    if ($notificationSent) {
                        $response['telegram_notification_sent'] = true;
                        $response['message'] = 'Audiencia creada correctamente y notificación enviada por Telegram.';
                    } else {
                        $response['telegram_notification_sent'] = false;
                        $response['message'] = 'Audiencia creada correctamente, pero hubo un problema al enviar la notificación por Telegram.';
                    }
                } else {
                    $response['telegram_notification_sent'] = false;
                    $response['message'] = 'Audiencia creada correctamente. Para recibir notificaciones por Telegram, configura tu chat ID en el perfil.';
                }
            } else {
                $response['message'] = 'Audiencia creada correctamente.';
            }

            $response['ok'] = true;
            return response()->json($response, 201);
        } catch (\Throwable $e) {
            report($e);
            $response['message'] = 'Ocurrió un problema al guardar.';
            return response()->json($response, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Audiencia $audiencia)
    {
        $this->authorize('update', $audiencia);
        $estatusLista = Estatus::statusWithOutProgramado();
        // Revisar si la audiencia ya pasó usando el método del modelo
        $audienciaPasada = Audiencia::isAudienciaPast($audiencia->fecha_audiencia, $audiencia->hora_fin_audiencia);

        // Si ya pasó, filtrar el estatus "programado"
        if ($audienciaPasada) {
            $estatusLista = $estatusLista->filter(function($estatus) {
                return strtolower($estatus->estatus) !== 'programado';
            });
        }
        $audiencia->fecha_audiencia = \Carbon\Carbon::parse($audiencia->fecha_audiencia)->format('Y-m-d');
        return view('audiencia.editar', compact('audiencia', 'estatusLista'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Audiencia $audiencia)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        $status = 422;
        // Normalizar horas
        $request->merge([
            'hora_audiencia'     => substr((string)$request->hora_audiencia, 0, 5),
            'hora_fin_audiencia' => substr((string)$request->hora_fin_audiencia, 0, 5),
        ]);

        $validator = Validator::make($request->all(), [
            'formValidationName'   => 'required|string|min:10|max:255',
            'formValidationAsunto' => 'required|string|min:10|max:255',
            'formValidationLugar'  => 'required|string|min:10|max:255',
            'formValidationFecha'  => 'required|date',
            'procedencia'          => 'nullable|string|max:255',
            'hora_audiencia'       => 'required|date_format:H:i',
            'hora_fin_audiencia'   => 'required|date_format:H:i|after:hora_audiencia',
            'estatus_id'           => 'required|exists:estatus,id',
            'descripcion'          => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            $response['message'] = 'Errores de validación.';
            $response['errors'] = $validator->errors();
            return response()->json($response, $status);
        }

        $validated = $validator->validated();

        // Duplicados (excluye el id actual)
        if (Audiencia::isAudienciaDuplicated($validated, Auth::user()->area_id, $audiencia->id)) {
            $response['message'] = 'Ya existe una audiencia con ese nombre en esa fecha y hora.';
            return response()->json($response, $status);
        }

        try {
            $audiencia->update([
                'nombre'            => $validated['formValidationName'],
                'asunto_audiencia'  => $validated['formValidationAsunto'],
                'lugar'             => $validated['formValidationLugar'],
                'fecha_audiencia'   => $validated['formValidationFecha'],
                'procedencia'       => $validated['procedencia'] ?? null,
                'hora_audiencia'    => $validated['hora_audiencia'],
                'hora_fin_audiencia' => $validated['hora_fin_audiencia'],
                'estatus_id'        => $validated['estatus_id'],
                'descripcion'       => $validated['descripcion'] ?? null,
            ]);

            $response['ok'] = true;
            $response['message'] = 'Audiencia actualizada correctamente.';
            return response()->json($response, 201);
        } catch (\Throwable $e) {
            report($e);
            $response['message'] = 'Ocurrió un problema al actualizar.';
            return response()->json($response, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Audiencia  $audiencia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audiencia $audiencia)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        $status = 500;
        if (auth()->id() !== $audiencia->user_id) {
            $response['message'] = 'No autorizado.';
            return response()->json($response, 403);
        }
        if (Audiencia::isAudienciaPast($audiencia->fecha_audiencia, $audiencia->hora_fin_audiencia)) {
            $response['message'] = 'No se puede eliminar una audiencia pasada.';
            return response()->json($response, 403);
        }
        try {
            $audiencia->delete();
            $response['ok'] = true;
            $response['message'] = 'Audiencia eliminada correctamente.';
            return response()->json($response);
        } catch (\Throwable $e) {
            report($e);
            $response['message'] = 'Ocurrió un problema al eliminar.';
            return response()->json($response, $status);
        }
    }
}
