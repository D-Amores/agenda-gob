<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estatus;
use App\Models\Audiencia;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

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
        $validated = $request->validate([
            'formValidationName' => 'required|string|min:10|max:255',
            'formValidationAsunto' => 'required|string|min:10|max:255',
            'formValidationLugar' => 'required|string|min:10|max:255',
            'formValidationFecha' => 'required|date',
            'procedencia' => 'nullable|string|max:255',
            'hora_audiencia' => 'required|date_format:H:i',
            'hora_fin_audiencia' => 'required|date_format:H:i|after:hora_audiencia',
            'estatus_id' => 'required|exists:estatus,id',
            'descripcion' => 'nullable|string|max:500',
        ]);

        try {
            $validated['formValidationFecha'] = date('Y-m-d', strtotime($validated['formValidationFecha']));
        } catch (\Exception $e) {
            Alert::error('Error', 'Fecha inválida')->autoClose(5000)->timerProgressBar();
            return redirect()->back()->withInput();
        }

        $exists = Audiencia::isAudienciaDuplicated($validated, Auth::user()->area_id);

        if ($exists) {
            Alert::warning('Advertencia', 'Ya existe una audiencia con ese nombre en esa fecha y hora.')->autoClose(5000)->timerProgressBar();
            return back()->withInput();
        }

        try {
            Audiencia::create([
                'nombre' => $validated['formValidationName'],
                'asunto_audiencia' => $validated['formValidationAsunto'],
                'lugar' => $validated['formValidationLugar'],
                'fecha_audiencia' => $validated['formValidationFecha'],
                'procedencia' => $validated['procedencia'] ?? null,
                'hora_audiencia' => $validated['hora_audiencia'],
                'hora_fin_audiencia' => $validated['hora_fin_audiencia'],
                'area_id' => Auth::user()->area_id,
                'estatus_id' => $validated['estatus_id'],
                'descripcion' => $validated['descripcion'] ?? null,
                'user_id' => Auth::id(),
            ]);

            Alert::success('Éxito', 'Guardado correctamente')->autoClose(5000)->timerProgressBar();
            return redirect()->route('calendario.index');
        } catch (\Exception $e) {
            Alert::error('Error', 'Ocurrió un problema al guardar.')->autoClose(7000)->timerProgressBar();
            return back()->withInput();
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
        $request->merge([
            'hora_audiencia' => substr($request->hora_audiencia, 0, 5), // Recorta a H:i
            'hora_fin_audiencia' => substr($request->hora_fin_audiencia, 0, 5),
        ]);

        $validated = $request->validate([
            'formValidationName' => 'required|string|min:10|max:255',
            'formValidationAsunto' => 'required|string|min:10|max:255',
            'formValidationLugar' => 'required|string|min:10|max:255',
            'formValidationFecha' => 'required|date',
            'procedencia' => 'nullable|string|max:255',
            'hora_audiencia' => 'required|date_format:H:i',
            'hora_fin_audiencia' => 'required|date_format:H:i|after:hora_audiencia',
            'estatus_id' => 'required|exists:estatus,id',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $exists = Audiencia::isAudienciaDuplicated($validated, Auth::user()->area_id, $audiencia->id);

        if ($exists) {
            Alert::warning('Advertencia', 'Ya existe una audiencia con ese nombre en esa fecha y hora.')->autoClose(5000)->timerProgressBar();
            return back()->withInput();
        }

        try {
            $audiencia->update([
                'nombre' => $validated['formValidationName'],
                'asunto_audiencia' => $validated['formValidationAsunto'],
                'lugar' => $validated['formValidationLugar'],
                'fecha_audiencia' => $validated['formValidationFecha'],
                'procedencia' => $validated['procedencia'] ?? null,
                'hora_audiencia' => $validated['hora_audiencia'],
                'hora_fin_audiencia' => $validated['hora_fin_audiencia'],
                'estatus_id' => $validated['estatus_id'],
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            Alert::success('Éxito', 'Audiencia actualizada correctamente')->autoClose(5000)->timerProgressBar();
            return redirect()->route('calendario.index');
        } catch (\Exception $e) {
            Alert::error('Error', 'No se pudo actualizar la audiencia')->autoClose(5000)->timerProgressBar();
            return back()->withInput();
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
