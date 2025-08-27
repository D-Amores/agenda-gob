<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estatus;
use App\Models\Audiencia;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        $estatusLista = Estatus::all();
        return view('audiencia.registro', compact('estatusLista'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validación manual para poder responder 422 en JSON uniformemente
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
            return response()->json([
                'ok'     => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Normalizar fecha
        try {
            $validated['formValidationFecha'] = Carbon::parse($validated['formValidationFecha'])->format('Y-m-d');
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Fecha inválida.',
            ], 422);
        }

        // Duplicados
        if (Audiencia::isAudienciaDuplicated($validated, Auth::user()->area_id)) {
            return response()->json([
                'ok'      => false,
                'message' => 'Ya existe una audiencia con ese nombre en esa fecha y hora.',
            ], 422);
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
                'estatus_id'        => $validated['estatus_id'],
                'descripcion'       => $validated['descripcion'] ?? null,
                'user_id'           => Auth::id(),
            ]);

            return response()->json([
                'ok'       => true,
                'message'  => 'Audiencia creada correctamente.',
                'audiencia' => [
                    'id'      => $audiencia->id,
                    'nombre'  => $audiencia->nombre,
                    'fecha'   => $audiencia->fecha_audiencia,
                    'hora'    => $audiencia->hora_audiencia,
                    'estatus' => optional($audiencia->estatus)->estatus,
                ],
            ], 201);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'ok'      => false,
                'message' => 'Ocurrió un problema al guardar.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
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
        $estatusLista = Estatus::all();
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
            return response()->json([
                'ok'     => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Duplicados (excluye el id actual)
        if (Audiencia::isAudienciaDuplicated($validated, Auth::user()->area_id, $audiencia->id)) {
            return response()->json([
                'ok'      => false,
                'message' => 'Ya existe una audiencia con ese nombre en esa fecha y hora.',
            ], 422);
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

            return response()->json([
                'ok'       => true,
                'message'  => 'Audiencia actualizada correctamente.',
                'audiencia' => [
                    'id'      => $audiencia->id,
                    'nombre'  => $audiencia->nombre,
                    'fecha'   => $audiencia->fecha_audiencia,
                    'hora'    => $audiencia->hora_audiencia,
                    'estatus' => optional($audiencia->estatus)->estatus,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo actualizar la audiencia.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
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
        if (auth()->id() !== $audiencia->user_id) {
            return response()->json([
                'ok'      => false,
                'message' => 'No autorizado.',
            ], 403);
        }

        try {
            $audiencia->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Audiencia eliminada correctamente.',
                'id'      => $audiencia->id,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo eliminar la audiencia.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
