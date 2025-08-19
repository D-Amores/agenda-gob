<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Estatus;
use App\Models\Audiencia;
use Illuminate\Support\Facades\Auth;

class AudienciaController extends Controller
{
    public function index(){
        $estatusLista = Estatus::all(); 

        return view('audiencia.registro', compact('estatusLista'));
    }

    public function crear(Request $request){
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

        $exists = Audiencia::where('nombre', $request->formValidationName)
        ->where('asunto_audiencia', $request->formValidationAsunto)
        ->whereDate('fecha_audiencia', $request->formValidationFecha)
        ->where('hora_audiencia', $request->hora_audiencia)
        ->exists();

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

    public function registrar(){
        $estatusLista = Estatus::all(); 
        return view('audiencia.registro', compact('estatusLista'));
    }

    public function editar(Audiencia $audiencia){
        $estatusLista = Estatus::all();
        $audiencia->fecha_audiencia = \Carbon\Carbon::parse($audiencia->fecha_audiencia)->format('Y-m-d');
        return view('audiencia.editar', compact('audiencia', 'estatusLista'));
    }

    public function actualizar(Request $request, Audiencia $audiencia){
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

        $exists = Audiencia::where('nombre', $request->formValidationName)
        ->where('asunto_audiencia', $request->formValidationAsunto)
        ->whereDate('fecha_audiencia', $request->formValidationFecha)
        ->where('hora_audiencia', $request->hora_audiencia)
        ->where('id', '!=', $audiencia->id)
        ->exists();

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

    public function eliminar(Audiencia $audiencia){
        try {
            $audiencia->delete();
            Alert::success('Éxito', 'Audiencia eliminada correctamente')->autoClose(5000)->timerProgressBar();
            return redirect()->route('calendario.index'); // aquí Laravel mostrará el alert
        } catch (\Exception $e) {
            Alert::error('Error', 'No se pudo eliminar la audiencia')->autoClose(5000)->timerProgressBar();
            return redirect()->route('calendario.index');
        }
    }

}
