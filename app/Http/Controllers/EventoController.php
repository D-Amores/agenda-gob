<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Models\Evento;
use App\Models\Estatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EventoController extends Controller
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
    public function create()
    {
        $estatusLista = Estatus::all();
        return view('evento.registro', compact('estatusLista'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEventoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventoRequest $request)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        $status = 422;

        try {
            $validated = $request->validated();
            $validated['formValidationFecha'] = date('Y-m-d', strtotime($validated['formValidationFecha']));
        } catch (ValidationException $e) {
            $response['message'] = 'Errores de validación.';
            $response['errors'] = $e->errors();
            return response()->json($response, $status);
        } catch (\Exception $e) {
            $response['message'] = 'Campos inválidos.';
            return response()->json($response, $status);
        }

        if (Evento::isEventoDuplicated($validated, Auth::user()->area_id)) {
            $response['message'] = 'Ya existe un Evento con ese nombre en esa fecha y hora.';
            return response()->json($response, $status);
        }

        try {
            Evento::create([
                'nombre' => $validated['formValidationName'],
                'asistencia_de_gobernador' => $validated['asistenciaGobernador'],
                'lugar' => $validated['formValidationLugar'],
                'fecha_evento' => $validated['formValidationFecha'],
                'hora_evento' => $validated['hora_evento'],
                'hora_fin_evento' => $validated['hora_fin_evento'],
                'vestimenta_id' => $validated['vestimenta'], // mapeo del input al campo
                'estatus_id' => $validated['estatus_id'],
                'descripcion' => $validated['descripcion'] ?? null,
                'area_id' => Auth::user()->area_id,
                'user_id' => Auth::id(),
            ]);

            $response['ok'] = true;
            $response['message'] = 'Evento creado correctamente.';
            return response()->json($response, 201);
        } catch (\Exception $e) {
            report($e);
            $response['message'] = 'Ocurrió un problema al guardar.';
            return response()->json($response, 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function show(Evento $evento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function edit(Evento $evento)
    {
        $estatusLista = Estatus::all();
        $evento->fecha_evento = \Carbon\Carbon::parse($evento->fecha_evento)->format('Y-m-d');
        return view('evento.editar', compact('evento', 'estatusLista'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventoRequest  $request
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventoRequest $request, Evento $evento)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        $status = 422;

        $validated = $request->validated();

        try {
            $validated = $request->validated();
        } catch (ValidationException $e) {
            $response['message'] = 'Errores de validación.';
            $response['errors'] = $e->errors();
            return response()->json($response, $status);
        }

        if (Evento::isEventoDuplicated($validated, Auth::user()->area_id, $evento->id)) {
            $response['message'] = 'Ya existe un Evento con ese nombre en esa fecha y hora.';
            return response()->json($response, $status);
        }

        try {
            $evento->update([
                'nombre' => $validated['formValidationName'],
                'asistencia_de_gobernador' => $validated['asistenciaGobernador'],
                'lugar' => $validated['formValidationLugar'],
                'fecha_evento' => $validated['formValidationFecha'],
                'hora_evento' => $validated['hora_evento'],
                'hora_fin_evento' => $validated['hora_fin_evento'],
                'vestimenta_id' => $validated['vestimenta'], // mapeo del input al campo
                'estatus_id' => $validated['estatus_id'],
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            $response['ok'] = true;
            $response['message'] = 'Evento actualizado correctamente.';
            return response()->json($response, 201);
        } catch (\Exception $e) {
            report($e);
            $response['message'] = 'Ocurrió un problema al guardar.';
            return response()->json($response, 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Evento $evento)
    {
        $response = ['ok' => false, 'message' => '', 'errors' => null];
        $status = 500;
        if (auth()->id() !== $evento->user_id) {
            $response['message'] = 'No autorizado.';
            return response()->json($response, 403);
        }

        try {
            $evento->delete();
            $response['ok'] = true;
            $response['message'] = 'Evento eliminado correctamente.';
            $response['id'] = $evento->id;
            return response()->json($response);
        } catch (\Throwable $e) {
            report($e);
            $response['message'] = 'Ocurrió un problema al eliminar.';
            return response()->json($response, $status);
        }
    }
}
