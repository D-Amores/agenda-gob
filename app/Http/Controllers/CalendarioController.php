<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index()
    {
        // Aquí puedes agregar la lógica para mostrar el calendario
        return view('calendario.calendario');
    }
}
