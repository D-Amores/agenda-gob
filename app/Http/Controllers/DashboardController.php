<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Audiencia;
use App\Models\Evento;

class DashboardController extends Controller
{
    public function index(){
        $numeroAudiencia = Audiencia::count();
        $numeroEventos = Evento::count();

        // Obtener las fechas de eventos últimos 7 días (o el rango que quieras)
        $fechasEventos = Evento::select(DB::raw('DATE(fecha_evento) as fecha'))
            ->whereDate('fecha_evento', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->pluck('fecha');

        // Obtener el conteo de audiencias por fecha (solo las fechas donde hay eventos)
        $audienciasPorFecha = Audiencia::select(DB::raw('DATE(fecha_audiencia) as fecha'), DB::raw('COUNT(*) as total'))
            ->whereIn(DB::raw('DATE(fecha_audiencia)'), $fechasEventos)
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        // Preparar datos para la vista
        $labels = [];
        $audienciasData = [];

        foreach ($fechasEventos as $fecha) {
            $labels[] = \Carbon\Carbon::parse($fecha)->format('d/m');
            $audienciasData[] = $audienciasPorFecha[$fecha] ?? 0; // si no hay audiencias, 0
        }

        return view('tablero', compact(
            'numeroAudiencia',
            'numeroEventos',
            'labels',
            'audienciasData'
        ));
    }
}
