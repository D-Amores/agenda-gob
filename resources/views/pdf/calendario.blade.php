<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Actividades</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
        }

        td {
            font-size: 9px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body>
    <h1>Calendario de Actividades</h1>

    <div class="info-header">
        <strong>Reporte generado el {{ date('d/m/Y H:i') }}</strong><br>
        @if ($fechaInicio || $fechaFin)
            <strong>Período:</strong>
            @if ($fechaInicio)
                Desde {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
            @endif
            @if ($fechaFin)
                @if ($fechaInicio)
                    hasta
                @else
                    Hasta
                @endif {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            @endif
        @else
            <strong>Período:</strong> Todas las fechas
        @endif
    </div>

        @if($incluirAudiencias && $audiencias->count() > 0)
        <h2>Audiencias</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Lugar</th>
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audiencias as $audiencia)
                    <tr>
                        <td>{{ $audiencia->nombre }}</td>
                        <td>{{ $audiencia->lugar }}</td>
                        <td>{{ $audiencia->asunto_audiencia }}</td>
                        <td>{{ \Carbon\Carbon::parse($audiencia->fecha_audiencia)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($audiencia->hora_audiencia)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($audiencia->hora_fin_audiencia)->format('H:i') }}</td>
                        <td>{{ $audiencia->estatus->estatus }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($incluirAudiencias)
        <h2>Audiencias</h2>
        <div class="no-data">No se encontraron audiencias para el período seleccionado.</div>
    @endif

    @if($incluirEventos && $eventos->count() > 0)
        <h2>Eventos</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Lugar</th>
                    <th>Asistencia Gobernador</th>
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($eventos as $evento)
                    <tr>
                        <td>{{ $evento->nombre }}</td>
                        <td>{{ $evento->lugar }}</td>
                        <td>{{ $evento->asistencia_de_gobernador == 1 ? 'Sí' : 'No' }}</td>
                        <td>{{ \Carbon\Carbon::parse($evento->fecha_evento)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($evento->hora_evento)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($evento->hora_fin_evento)->format('H:i') }}</td>
                        <td>{{ $evento->estatus->estatus }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($incluirEventos)
        <h2>Eventos</h2>
        <div class="no-data">No se encontraron eventos para el período seleccionado.</div>
    @endif

    @if (!$incluirAudiencias && !$incluirEventos)
        <div class="no-data">
            <h2>No se seleccionó ningún tipo de contenido</h2>
            <p>Por favor, selecciona al menos "Audiencias" o "Eventos" para generar el reporte.</p>
        </div>
    @endif
</body>

</html>
