<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Audiencia;
use App\Models\Evento;

class DashboardController extends Controller
{
    public function index()
    {
        $numeroAudiencia = Audiencia::count();
        $numeroEventos = Evento::count();

        // Fechas recientes de eventos
        $fechasEventos = Evento::select(DB::raw('DATE(fecha_evento) as fecha'))
            ->whereDate('fecha_evento', '>=', now()->subDays(6)->startOfDay());

        // Fechas recientes de audiencias
        $fechasAudiencias = Audiencia::select(DB::raw('DATE(fecha_audiencia) as fecha'))
            ->whereDate('fecha_audiencia', '>=', now()->subDays(6)->startOfDay());

        // Combinar fechas y ordenar
        $fechasTodas = $fechasEventos
            ->union($fechasAudiencias)
            ->orderBy('fecha', 'asc')
            ->pluck('fecha');

        // Contar eventos y audiencias por fecha
        $eventosData = [];
        $audienciasData = [];

        foreach($fechasTodas as $fecha) {
            $eventosData[] = Evento::whereDate('fecha_evento', $fecha)->count();
            $audienciasData[] = Audiencia::whereDate('fecha_audiencia', $fecha)->count();
        }

        return view('tablero', compact(
            'numeroAudiencia',
            'numeroEventos',
            'fechasTodas',
            'eventosData',
            'audienciasData'
        ));
    }
}
