<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use App\Models\Evento;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PdfController extends Controller
{
    public function download(Request $request)
    {
        // Obtener parámetros del formulario
        $incluirAudiencias = $request->has('incluir_audiencias');
        $incluirEventos = $request->has('incluir_eventos');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Obtener el área del usuario autenticado
        $userAreaId = auth()->user()->area_id;

        // Inicializar consultas
        $audiencias = collect();
        $eventos = collect();

        // Obtener audiencias si está seleccionado
        if ($incluirAudiencias) {
            $queryAudiencias = Audiencia::with(['estatus', 'area'])
                ->where('area_id', $userAreaId); // Filtrar por área del usuario

            if ($fechaInicio) {
                $queryAudiencias->where('fecha_audiencia', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $queryAudiencias->where('fecha_audiencia', '<=', $fechaFin);
            }

            $audiencias = $queryAudiencias->get();
        }

        // Obtener eventos si está seleccionado
        if ($incluirEventos) {
            $queryEventos = Evento::with(['estatus', 'vestimenta', 'area'])
                ->where('area_id', $userAreaId); // Filtrar por área del usuario

            if ($fechaInicio) {
                $queryEventos->where('fecha_evento', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $queryEventos->where('fecha_evento', '<=', $fechaFin);
            }

            $eventos = $queryEventos->get();
        }

        $data = [
            'audiencias' => $audiencias,
            'eventos' => $eventos,
            'incluirAudiencias' => $incluirAudiencias,
            'incluirEventos' => $incluirEventos,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'areaUsuario' => auth()->user()->area->area ?? 'Sin área asignada',
        ];

        $pdf = PDF::loadView('pdf.calendario', $data);

        // Generar nombre del archivo basado en filtros
        $nombreArchivo = 'calendario_' . str_replace(' ', '_', strtolower(auth()->user()->area->area ?? 'general'));
        if ($fechaInicio || $fechaFin) {
            $nombreArchivo .= '_filtrado';
        }
        $nombreArchivo .= '.pdf';

        return $pdf->stream($nombreArchivo);
    }
}
