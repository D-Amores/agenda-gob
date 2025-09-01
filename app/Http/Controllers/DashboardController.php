<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Audiencia;
use App\Models\Evento;
use Carbon\Carbon;
use Exception;

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

        // Queries base (toda el área, futuros) — excluir cancelados (estatus_id = 3)
        $eventosQueryBase = Evento::where('area_id', $areaId)
            ->where('estatus_id', '<>', 3)
            ->whereDate('fecha_evento', '>=', now()->startOfDay());
        $audienciasQueryBase = Audiencia::where('area_id', $areaId)
            ->where('estatus_id', '<>', 3)
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

    /**
     * Endpoint para devolver datos JSON para la gráfica según filtro.
     * Rutas: GET /dashboard/chart-data?filter=... [&start=YYYY-MM-DD&end=YYYY-MM-DD]
     */
    public function chartData(Request $request)
    {
        try {
            $user = auth()->user();
            $areaId = $user->area_id;
            $userId = $user->id;

            $filter = $request->query('filter', 'mis');
            $start = $request->query('start');
            $end = $request->query('end');

            // Queries base (siempre filtrar por área) — excluir cancelados (estatus_id = 3)
            $eventosBase = Evento::where('area_id', $areaId)->where('estatus_id', '<>', 3);
            $audienciasBase = Audiencia::where('area_id', $areaId)->where('estatus_id', '<>', 3);

            $today = Carbon::now()->startOfDay();

            // default range (used when no explicit start/end provided)
            $rangeStart = $today->copy();
            $rangeEnd = $today->copy()->addDays(29); // 30 días por defecto

            switch ($filter) {
                case 'mis':
                    $eventosBase->where('user_id', $userId);
                    $audienciasBase->where('user_id', $userId);
                    // do not force future; we'll compute real min/max below when appropriate
                    break;

                case 'area':
                    // don't force future here — user requested full range from first to last record
                    // (still scoped by area above)
                    break;

                case '7dias':
                    $rangeStart = $today->copy();
                    $rangeEnd = $today->copy()->addDays(6);
                    break;

                case '30dias':
                    $rangeStart = $today->copy();
                    $rangeEnd = $today->copy()->addDays(29);
                    break;

                case 'personalizado':
                    if (!$start || !$end) {
                        return response()->json(['message' => 'start and end dates are required for personalizado'], 422);
                    }
                    try {
                        $rangeStart = Carbon::parse($start)->startOfDay();
                        $rangeEnd = Carbon::parse($end)->startOfDay();
                    } catch (Exception $e) {
                        return response()->json(['message' => 'invalid date format'], 422);
                    }
                    if ($rangeEnd->lt($rangeStart)) {
                        return response()->json(['message' => 'end must be after or equal to start'], 422);
                    }
                    break;

                default:
                    // fallback to future 30 days for unknown filter
                    $eventosBase->whereDate('fecha_evento', '>=', $today);
                    $audienciasBase->whereDate('fecha_audiencia', '>=', $today);
                    $rangeStart = $today->copy();
                    $rangeEnd = $today->copy()->addDays(29);
                    break;
            }

            // Para 'mis' y 'area' (cuando no se pasó start/end) usar fechas registradas
            // pero SIN mostrar fechas anteriores a hoy (start = max(minDate, today)).
            if (in_array($filter, ['mis', 'area']) && !$start && !$end) {
                $minEvento = (clone $eventosBase)->select(DB::raw('MIN(DATE(fecha_evento)) as min_date'))->value('min_date');
                $minAud = (clone $audienciasBase)->select(DB::raw('MIN(DATE(fecha_audiencia)) as min_date'))->value('min_date');
                $maxEvento = (clone $eventosBase)->select(DB::raw('MAX(DATE(fecha_evento)) as max_date'))->value('max_date');
                $maxAud = (clone $audienciasBase)->select(DB::raw('MAX(DATE(fecha_audiencia)) as max_date'))->value('max_date');

                $minDates = array_filter([$minEvento, $minAud]);
                $maxDates = array_filter([$maxEvento, $maxAud]);

                if (!empty($minDates) && !empty($maxDates)) {
                    $minDate = Carbon::parse(min($minDates))->startOfDay();
                    $maxDate = Carbon::parse(max($maxDates))->startOfDay();

                    if ($maxDate->gte($today)) {
                        // start debe ser al menos hoy
                        $rangeStart = $minDate->gte($today) ? $minDate : $today->copy();
                        $rangeEnd = $maxDate;
                    } else {
                        // todas las fechas están en el pasado -> ventana corta desde hoy
                        $rangeStart = $today->copy();
                        $rangeEnd = $today->copy()->addDays(6);
                    }
                } else {
                    // sin registros -> ventana corta desde hoy
                    $rangeStart = $today->copy();
                    $rangeEnd = $today->copy()->addDays(6);
                }
            }

            // Build list of dates inclusive
            $fechas = [];
            $cursor = $rangeStart->copy();
            while ($cursor->lte($rangeEnd)) {
                $fechas[] = $cursor->toDateString();
                $cursor->addDay();
            }

            $eventosData = [];
            $audienciasData = [];

            foreach ($fechas as $f) {
                $eventosCount = (clone $eventosBase)->whereDate('fecha_evento', $f)->count();
                $audienciasCount = (clone $audienciasBase)->whereDate('fecha_audiencia', $f)->count();

                $eventosData[] = $eventosCount;
                $audienciasData[] = $audienciasCount;
            }

            return response()->json([
                'fechas' => $fechas,
                'audiencias' => $audienciasData,
                'eventos' => $eventosData,
            ]);
        } catch (Exception $ex) {
            return response()->json(['message' => 'server error'], 500);
        }
    }
}
