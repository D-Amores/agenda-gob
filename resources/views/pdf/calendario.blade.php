<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Actividades - Reporte Oficial</title>
    <style>
        /* Estilos optimizados para múltiples líneas */
        body {
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #2c3e50;
        }

        .header h1 {
            color: #2c3e50;
            margin: 0 0 4px 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        .header .subtitle {
            color: #7f8c8d;
            font-size: 10px;
            margin: 0;
        }

        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
            table-layout: fixed;
        }

        th {
            background: #2c3e50;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            vertical-align: top;
        }

        td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            font-size: 8px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .section-title {
            background-color: #3498db;
            color: white;
            padding: 6px 8px;
            margin: 15px 0 10px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 15px;
            color: #7f8c8d;
            font-style: italic;
            background-color: #f8f9fa;
            border: 1px dashed #bdc3c7;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 9px;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: bold;
            white-space: nowrap;
        }

        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #7f8c8d;
        }

        /* Estilos para múltiples líneas */
        .multi-line {
            white-space: normal;
            line-height: 1.4;
            min-height: 20px;
        }

        .compact-cell {
            padding: 4px 3px;
        }

        .summary {
            font-size: 8px;
            margin: 8px 0 12px 0;
            text-align: right;
        }

        /* Anchuras de columnas optimizadas */
        .col-xsmall { width: 7%; }
        .col-small { width: 9%; }
        .col-medium { width: 15%; }
        .col-large { width: 20%; }
        .col-xlarge { width: 25%; }

        /* Altura de filas flexible */
        tr {
            height: auto;
        }

        @media print {
            body {
                padding: 8px;
                margin: 0;
            }

            /* Mejor manejo de saltos de página */
            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            td, th {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>Calendario de Actividades</h1>
        <p class="subtitle">Secretaría Anticorrupción y Buen Gobierno</p>
    </div>

    <!-- Información del reporte -->
    <div class="info-box">
        <strong>Generado:</strong> {{ date('d/m/Y H:i') }} |
        <strong>Período:</strong>
        @if ($fechaInicio || $fechaFin)
            @if ($fechaInicio){{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}@endif
            @if ($fechaFin)@if ($fechaInicio)-@else>@endif{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}@endif
        @else
            Todas las fechas
        @endif
    </div>

    <!-- Sección de Audiencias -->
    @if($incluirAudiencias && $audiencias->count() > 0)
        <div class="section-title">AUDIENCIAS ({{ $audiencias->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th class="col-xlarge">Nombre</th>
                    <th class="col-large">Lugar</th>
                    <th class="col-xlarge">Asunto</th>
                    <th class="col-small">Fecha</th>
                    <th class="col-xsmall">Hora Inicio</th>
                    <th class="col-xsmall">Hora Fin</th>
                    <th class="col-xsmall">Duración</th>
                    <th class="col-small">Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audiencias as $audiencia)
                    <tr>
                        <td class="multi-line"><strong>{{ $audiencia->nombre }}</strong></td>
                        <td class="multi-line">{{ $audiencia->lugar }}</td>
                        <td class="multi-line">{{ $audiencia->asunto_audiencia }}</td>
                        <td class="compact-cell">{{ \Carbon\Carbon::parse($audiencia->fecha_audiencia)->format('d/m/Y') }}</td>
                        <td class="compact-cell">{{ \Carbon\Carbon::parse($audiencia->hora_audiencia)->format('H:i') }}</td>
                        <td class="compact-cell">{{ \Carbon\Carbon::parse($audiencia->hora_fin_audiencia)->format('H:i') }}</td>
                        <td class="compact-cell">
                            @php
                                $inicio = \Carbon\Carbon::parse($audiencia->hora_audiencia);
                                $fin = \Carbon\Carbon::parse($audiencia->hora_fin_audiencia);
                                echo $inicio->diff($fin)->format('%H:%I');
                            @endphp
                        </td>
                        <td class="compact-cell">
                            @php
                                $estatus = strtolower($audiencia->estatus->estatus);
                                $estatusClass = 'badge-secondary';
                                if(strpos($estatus, 'confirm') !== false) $estatusClass = 'badge-success';
                                elseif(strpos($estatus, 'pendiente') !== false) $estatusClass = 'badge-warning';
                                elseif(strpos($estatus, 'cancel') !== false) $estatusClass = 'badge-danger';
                            @endphp
                            <span class="badge {{ $estatusClass }}">{{ $audiencia->estatus->estatus }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($incluirAudiencias)
        <div class="section-title">AUDIENCIAS</div>
        <div class="no-data">No se encontraron audiencias</div>
    @endif

    <!-- Sección de Eventos -->
    @if($incluirEventos && $eventos->count() > 0)
        <div class="section-title">EVENTOS ({{ $eventos->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th class="col-xlarge">Nombre</th>
                    <th class="col-large">Lugar</th>
                    <th class="col-xsmall">Gobernador</th>
                    <th class="col-small">Fecha</th>
                    <th class="col-xsmall">Hora Inicio</th>
                    <th class="col-xsmall">Hora Fin</th>
                    <th class="col-xsmall">Duración</th>
                    <th class="col-small">Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($eventos as $evento)
                    <tr>
                        <td class="multi-line"><strong>{{ $evento->nombre }}</strong></td>
                        <td class="multi-line">{{ $evento->lugar }}</td>
                        <td class="compact-cell">
                            @if($evento->asistencia_de_gobernador == 1)
                                <span class="badge badge-success">Sí</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </td>
                        <td class="compact-cell">{{ \Carbon\Carbon::parse($evento->fecha_evento)->format('d/m/Y') }}</td>
                        <td class="compact-cell">{{ \Carbon\Carbon::parse($evento->hora_evento)->format('H:i') }}</td>
                        <td class="compact-cell">{{ \Carbon\Carbon::parse($evento->hora_fin_evento)->format('H:i') }}</td>
                        <td class="compact-cell">
                            @php
                                $inicio = \Carbon\Carbon::parse($evento->hora_evento);
                                $fin = \Carbon\Carbon::parse($evento->hora_fin_evento);
                                echo $inicio->diff($fin)->format('%H:%I');
                            @endphp
                        </td>
                        <td class="compact-cell">
                            @php
                                $estatus = strtolower($evento->estatus->estatus);
                                $estatusClass = 'badge-secondary';
                                if(strpos($estatus, 'confirm') !== false) $estatusClass = 'badge-success';
                                elseif(strpos($estatus, 'pendiente') !== false) $estatusClass = 'badge-warning';
                                elseif(strpos($estatus, 'cancel') !== false) $estatusClass = 'badge-danger';
                            @endphp
                            <span class="badge {{ $estatusClass }}">{{ $evento->estatus->estatus }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            Con asistencia de gobernador: {{ $eventos->where('asistencia_de_gobernador', 1)->count() }}
        </div>
    @elseif($incluirEventos)
        <div class="section-title">EVENTOS</div>
        <div class="no-data">No se encontraron eventos</div>
    @endif

    @if (!$incluirAudiencias && !$incluirEventos)
        <div class="no-data">
            No se seleccionó ningún tipo de contenido para el reporte.
        </div>
    @endif

    <!-- Pie de página -->
    <div class="footer">
        Sistema de Gestión de Agenda y Eventos • {{ date('d/m/Y') }}
    </div>
</body>
</html>
