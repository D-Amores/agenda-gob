<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Estatus;
use App\Models\Audiencia;

class AudienciaController extends Controller
{
    public function index(){
        $estatusList = Estatus::all(); 

        return view('audiencia.registro', compact('estatusList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'formValidationName' => 'required|string|max:255',
            'formValidationAsunto' => 'required|string|max:255',
            'formValidationLugar' => 'required|string|max:255',
            'formValidationFecha' => 'required|date',
            'procedencia' => 'nullable|string|max:255',
            'hora_audiencia' => 'required|date_format:H:i',
            'area_id' => 'required|in:1,2,3',
            'estatus_id' => 'required|exists:estatus,id',
            'descripcion' => 'nullable|string|max:500',
        ]);

        try {
            $validated['formValidationFecha'] = date('Y-m-d', strtotime($validated['formValidationFecha']));
        } catch (\Exception $e) {
            Alert::error('Error', 'Fecha inválida')->autoClose(5000)->timerProgressBar();
            return redirect()->back()->withInput();
        }

        try{
            Audiencia::create([
                'nombre' => $request->formValidationName,
                'asunto_audiencia' => $request->formValidationAsunto,
                'lugar' => $request->formValidationLugar,
                'fecha_audiencia' => $request->formValidationFecha,
                'procedencia' => $request->procedencia,
                'hora_audiencia' => $request->hora_audiencia,
                'area_id' => $request->area_id,
                'estatus_id' => $request->estatus_id,
                'descripcion' => $request->descripcion,
                'user_id' => 1, // o como estés manejando al usuario
            ]);
    
            // Audiencia::create($validated);
            Alert::success('Éxito', 'Guardado correctamente')->autoClose(5000)->timerProgressBar();;
            return redirect()->route('audiencias.registro');
        } catch (\Exception $e) {
            Alert::error('Error', 'Ocurrió un problema al guardar.')->autoClose(7000)->timerProgressBar();
            return back()->withInput();
        }
    }

}
