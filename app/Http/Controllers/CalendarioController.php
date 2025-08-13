<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audiencia;

class CalendarioController extends Controller
{
    public function index()
    {
        $audiencias = Audiencia::all();
        // Aquí puedes agregar la lógica para mostrar el calendario
        return view('calendario.calendario', compact('audiencias'));
    }
}
