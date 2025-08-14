<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audiencia;
use App\Models\Evento;

class CalendarioController extends Controller
{
    public function index()
    {
        $eventos = Evento::all();
        $audiencias = Audiencia::all();
        // Aquí puedes agregar la lógica para mostrar el calendario
        return view('calendario.calendario', compact('audiencias', 'eventos'));	
    }
}
