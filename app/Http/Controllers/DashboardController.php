<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Audiencia;

class DashboardController extends Controller
{
    public function dashboard(){
        $numeroAudiencia = Audiencia::all()->count();
        $numeroEventos = Evento::all()->count();

        return view('tablero', compact('numeroAudiencia', 'numeroEventos'));
    }
}
