<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Audiencia;
use App\Models\Evento;

class DashboardController extends Controller
{
        public function __construct()
    {
        // Solo aplica 'auth' a las rutas que NO sean login
        $this->middleware('auth');
    }

    public function index()
    {
        $areaId = auth()->user()->area_id;

        $numeroEventos = Evento::where('area_id', $areaId)->count();
        $numeroAudiencia = Audiencia::where('area_id', $areaId)->count();

        // Fechas recientes de eventos del área
        $fechasEventos = Evento::select(DB::raw('DATE(fecha_evento) as fecha'))
            ->where('area_id', $areaId)
            ->whereDate('fecha_evento', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(fecha_evento)'));

        // Fechas recientes de audiencias del área
        $fechasAudiencias = Audiencia::select(DB::raw('DATE(fecha_audiencia) as fecha'))
            ->where('area_id', $areaId)
            ->whereDate('fecha_audiencia', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(fecha_audiencia)'));

        // Combinar fechas y ordenar
        $fechasTodas = $fechasEventos
            ->union($fechasAudiencias)
            ->orderBy('fecha', 'asc')
            ->pluck('fecha');
        
        $eventosData = [];
        $audienciasData = [];

        foreach($fechasTodas as $fecha) {
            $eventosData[] = Evento::where('area_id', $areaId)->whereDate('fecha_evento', $fecha)->count();
            $audienciasData[] = Audiencia::where('area_id', $areaId)->whereDate('fecha_audiencia', $fecha)->count();
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
