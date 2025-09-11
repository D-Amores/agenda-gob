<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Audiencia;
use App\Models\Evento;
use App\Models\Estatus;
use Carbon\Carbon;

class CambiarEstatusVencidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventos:cambiar-estatus-vencidos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cambia automáticamente a "cancelado" los eventos y audiencias que pasaron 1 hora después de su finalización sin ser marcados como atendidos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("🕐 Ejecutando a las: " . $now->format('Y-m-d H:i:s'));
        
        $estatusCancelado = Estatus::where('estatus', 'Cancelado')->first();
        
        if (!$estatusCancelado) {
            $this->error('No se encontró el estatus "Cancelado" en la base de datos');
            \Log::error('CambiarEstatusVencidos: No se encontró el estatus Cancelado');
            return 1;
        }

        $this->info('Iniciando verificación de eventos y audiencias vencidos...');

        // Procesar Audiencias
        $audienciasVencidas = Audiencia::with(['estatus'])
            ->whereHas('estatus', function($query) {
                $query->whereIn('estatus', ['Programado', 'Reprogramado']);
            })
            ->get();

        $audienciasActualizadas = 0;
        foreach ($audienciasVencidas as $audiencia) {
            $horaFinalizacion = Carbon::parse($audiencia->fecha_audiencia . ' ' . $audiencia->hora_fin_audiencia);
            $tiempoLimite = $horaFinalizacion->copy()->addHour();
            
            if ($now->greaterThan($tiempoLimite)) {
                $audiencia->update(['estatus_id' => $estatusCancelado->id]);
                $audienciasActualizadas++;
                $this->line("Audiencia ID {$audiencia->id} - '{$audiencia->nombre}' cambió a Cancelado");
            }
        }

        // Procesar Eventos
        $eventosVencidos = Evento::with(['estatus'])
            ->whereHas('estatus', function($query) {
                $query->whereIn('estatus', ['Programado', 'Reprogramado']);
            })
            ->get();

        $eventosActualizados = 0;
        foreach ($eventosVencidos as $evento) {
            $horaFinalizacion = Carbon::parse($evento->fecha_evento . ' ' . $evento->hora_fin_evento);
            $tiempoLimite = $horaFinalizacion->copy()->addHour();
            
            if ($now->greaterThan($tiempoLimite)) {
                $evento->update(['estatus_id' => $estatusCancelado->id]);
                $eventosActualizados++;
                $this->line("Evento ID {$evento->id} - '{$evento->nombre}' cambió a Cancelado");
            }
        }

        $this->info("Proceso completado:");
        $this->info("- Audiencias actualizadas: {$audienciasActualizadas}");
        $this->info("- Eventos actualizados: {$eventosActualizados}");
        $this->info("Total: " . ($audienciasActualizadas + $eventosActualizados) . " registros actualizados");

        // Log para seguimiento
        if (($audienciasActualizadas + $eventosActualizados) > 0) {
            \Log::info("CambiarEstatusVencidos: Se cancelaron {$audienciasActualizadas} audiencias y {$eventosActualizados} eventos");
        }

        return 0;
    }
}
