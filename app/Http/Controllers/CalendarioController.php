<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audiencia;
use App\Models\Evento;
use RealRashid\SweetAlert\Facades\Alert;

class CalendarioController extends Controller
{
    public function index()
    {
        $eventos = Evento::with(['estatus', 'user', 'vestimenta'])->get();
        $audiencias = Audiencia::with(['estatus', 'user'])->get();

        // Aquí puedes agregar la lógica para mostrar el calendario
        return view('calendario.calendario', compact('audiencias', 'eventos'));	
    }

    //Funciones de ejemplo para eliminar
    public function destroyAudiencia(Request $request){
        $id = $request->input('id');
        return response()->json(['message' => 'Audiencia eliminada: ' . $id]);
    }

    public function destroyEvento(Request $request){
        $id = $request->input('id');
        return response()->json(['message' => 'Evento eliminado:' . $id]);
    }
}
