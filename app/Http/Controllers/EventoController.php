<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Estatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class EventoController extends Controller
{
    public function index(){
        $estatusLista = Estatus::all();
        return view('evento.registro', compact('estatusLista'));
    }

    public function crear(Request $request){
        $validated = $request->validate([
            'formValidationName' => 'required|string|min:10|max:255',
            'asistenciaGobernador' => ['required', 'in:1,0'],
            'formValidationLugar' => 'required|string|min:10|max:255',
            'formValidationFecha' => 'required|date',
            'vestimenta' => ['required', 'exists:vestimentas,id'],
            'hora_evento' => 'required|date_format:H:i',
            'hora_fin_evento' => 'required|date_format:H:i|after:hora_evento',
            'estatus_id' => 'required|exists:estatus,id',
            'descripcion' => 'nullable|string|max:500',
        ]);

        try {
            $validated['formValidationFecha'] = date('Y-m-d', strtotime($validated['formValidationFecha']));
        } catch (\Exception $e) {
            Alert::error('Error', 'Fecha inválida')->autoClose(5000)->timerProgressBar();
            return redirect()->back()->withInput();
        }

        $exists = Evento::where('nombre', $request->formValidationName)
        ->whereDate('fecha_evento', $request->formValidationFecha)
        ->where('hora_evento', $request->hora_evento)
        ->exists();

        if ($exists) {
            Alert::warning('Advertencia', 'Ya existe una audiencia con ese nombre en esa fecha y hora.')->autoClose(5000)->timerProgressBar();
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

    public function registrar(){
        $estatusLista = Estatus::all();
        return view('evento.registro', compact('estatusLista'));
    }
}
