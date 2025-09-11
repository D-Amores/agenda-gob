<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use App\Models\Evento;
use App\Models\Estatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class CalendarioController extends Controller
{
    public function index()
    {
        // Ejecutar verificación de estatus vencidos antes de mostrar el calendario
        Artisan::call('eventos:cambiar-estatus-vencidos');

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
        $estatus = Estatus::all();

        // Aquí puedes agregar la lógica para mostrar el calendario
        return view('calendario.calendario', compact('audiencias', 'eventos', 'estatus'));
    }
}
