<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audiencia;
use App\Models\Evento;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CalendarioController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $areaId = $user->area_id;

        // Filtra eventos del área
        $eventos = Evento::with(['estatus', 'user', 'vestimenta'])
            ->where('area_id', $areaId)
            ->get();

        // Filtra audiencias del área
        $audiencias = Audiencia::with(['estatus', 'user'])
            ->where('area_id', $areaId)
            ->get();

        // Aquí puedes agregar la lógica para mostrar el calendario
        return view('calendario.calendario', compact('audiencias', 'eventos'));	
    }
}
