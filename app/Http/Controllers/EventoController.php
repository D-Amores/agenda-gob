<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Models\Evento;
use App\Models\Estatus;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

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
        $validated = $request->validated();

        try {
            $validated['formValidationFecha'] = date('Y-m-d', strtotime($validated['formValidationFecha']));
        } catch (\Exception $e) {
            Alert::error('Error', 'Fecha inválida')->autoClose(5000)->timerProgressBar();
            return redirect()->back()->withInput();
        }

        $exists = Evento::isEventoDuplicated($validated, Auth::user()->area_id);

        if ($exists) {
            Alert::warning('Advertencia', 'Ya existe un Evento con ese nombre en esa fecha y hora.')->autoClose(5000)->timerProgressBar();
            return back()->withInput();
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

        $validated = $request->validated();

        $exists = Evento::isEventoDuplicated($validated, Auth::user()->area_id, $evento->id);

        if ($exists) {
            Alert::warning('Advertencia', 'Ya existe un Evento con ese nombre en esa fecha y hora.')->autoClose(5000)->timerProgressBar();
            return back()->withInput();
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

            Alert::success('Éxito', 'Evento actualizado correctamente')->autoClose(5000)->timerProgressBar();
            return redirect()->route('calendario.index');
        } catch (\Exception $e) {
            Alert::error('Error', 'No se pudo actualizar el Evento')->autoClose(5000)->timerProgressBar();
            return back()->withInput();
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
        if (auth()->id() !== $evento->user_id) {
            return response()->json([
                'ok'      => false,
                'message' => 'No autorizado.',
            ], 403);
        }

        try {
            $evento->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Evento eliminado correctamente.',
                'id'      => $evento->id,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo eliminar el Evento.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
