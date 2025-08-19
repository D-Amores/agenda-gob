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
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $areaId = $user->area_id;
        $userId = $user->id;

        // Queries base (toda el área, futuros)
        $eventosQueryBase = Evento::where('area_id', $areaId)
                                ->whereDate('fecha_evento', '>=', now()->startOfDay());
        $audienciasQueryBase = Audiencia::where('area_id', $areaId)
                                        ->whereDate('fecha_audiencia', '>=', now()->startOfDay());

        // Contar eventos y audiencias personales
        $numeroEventos = (clone $eventosQueryBase)->where('user_id', $userId)->count();
        $numeroAudiencia = (clone $audienciasQueryBase)->where('user_id', $userId)->count();

        if ($numeroEventos > 0 || $numeroAudiencia > 0) {
            $tituloGrafica = "Tus eventos y audiencias";
            $eventosQuery = (clone $eventosQueryBase)->where('user_id', $userId);
            $audienciasQuery = (clone $audienciasQueryBase)->where('user_id', $userId);

            // Contadores solo del usuario
            $numeroEventos = (clone $eventosQuery)->count();
            $numeroAudiencia = (clone $audienciasQuery)->count();
        } else {
            $tituloGrafica = "Eventos y audiencias del área: " . $user->area->area;
            $eventosQuery = clone $eventosQueryBase;       // sin filtrar por usuario
            $audienciasQuery = clone $audienciasQueryBase; // sin filtrar por usuario

            // Contadores de toda el área
            $numeroEventos = (clone $eventosQuery)->count();
            $numeroAudiencia = (clone $audienciasQuery)->count();
        }


        // Fechas recientes de eventos y audiencias
        $fechasEventos = (clone $eventosQuery)
            ->select(DB::raw('DATE(fecha_evento) as fecha'))
            ->groupBy(DB::raw('DATE(fecha_evento)'));

        $fechasAudiencias = (clone $audienciasQuery)
            ->select(DB::raw('DATE(fecha_audiencia) as fecha'))
            ->groupBy(DB::raw('DATE(fecha_audiencia)'));

        // Combinar fechas y ordenar
        $fechasTodas = $fechasEventos
            ->union($fechasAudiencias)
            ->orderBy('fecha', 'asc')
            ->pluck('fecha');

        // Datos para la gráfica
        $eventosData = [];
        $audienciasData = [];

        foreach ($fechasTodas as $fecha) {
            $eventosData[] = (clone $eventosQuery)
                                ->whereDate('fecha_evento', $fecha)
                                ->count();
            $audienciasData[] = (clone $audienciasQuery)
                                ->whereDate('fecha_audiencia', $fecha)
                                ->count();
        }

        return view('tablero', compact(
            'numeroAudiencia',
            'numeroEventos',
            'fechasTodas',
            'eventosData',
            'audienciasData',
            'tituloGrafica'
        ));
    }
}
