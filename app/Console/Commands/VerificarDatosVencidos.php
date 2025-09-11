<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Audiencia;
use App\Models\Evento;
use App\Models\Estatus;
use Carbon\Carbon;

class VerificarDatosVencidos extends Command
{
    protected $signature = 'eventos:verificar-datos-vencidos';
    protected $description = 'Verifica qué audiencias y eventos están vencidos y su estatus actual';

    public function handle()
    {
        $now = Carbon::now();
        $this->info("🕐 Fecha y hora actual: " . $now->format('Y-m-d H:i:s'));
        $this->info("==========================================");

        // Verificar audiencias
        $this->line("🎯 VERIFICANDO AUDIENCIAS:");
        $audiencias = Audiencia::with(['estatus'])->get();
        
        if ($audiencias->count() === 0) {
            $this->warn("No hay audiencias en la base de datos");
        } else {
            foreach ($audiencias as $audiencia) {
                $fechaHoraFin = Carbon::parse($audiencia->fecha_audiencia . ' ' . $audiencia->hora_fin_audiencia);
                $tiempoLimite = $fechaHoraFin->copy()->addHour();
                $estatusActual = $audiencia->estatus->estatus ?? 'Sin estatus';
                
                $this->line("📅 ID: {$audiencia->id} - {$audiencia->nombre}");
                $this->line("   📍 Finaliza: " . $fechaHoraFin->format('Y-m-d H:i:s'));
                $this->line("   ⏰ Límite (+ 1h): " . $tiempoLimite->format('Y-m-d H:i:s'));
                $this->line("   📊 Estatus: {$estatusActual}");
                
                if ($now->greaterThan($tiempoLimite)) {
                    if (in_array($estatusActual, ['Programado', 'Reprogramado'])) {
                        $this->error("   ❌ DEBE SER CANCELADA - Pasó el tiempo límite y está en estatus válido");
                    } else {
                        $this->info("   ✅ No se cancela - Estatus: {$estatusActual}");
                    }
                } else {
                    $minutosRestantes = $now->diffInMinutes($tiempoLimite);
                    $this->info("   ⏳ Faltan {$minutosRestantes} minutos para vencer");
                }
                $this->line("");
            }
        }

        // Verificar eventos
        $this->line("🎯 VERIFICANDO EVENTOS:");
        $eventos = Evento::with(['estatus'])->get();
        
        if ($eventos->count() === 0) {
            $this->warn("No hay eventos en la base de datos");
        } else {
            foreach ($eventos as $evento) {
                $fechaHoraFin = Carbon::parse($evento->fecha_evento . ' ' . $evento->hora_fin_evento);
                $tiempoLimite = $fechaHoraFin->copy()->addHour();
                $estatusActual = $evento->estatus->estatus ?? 'Sin estatus';
                
                $this->line("📅 ID: {$evento->id} - {$evento->nombre}");
                $this->line("   📍 Finaliza: " . $fechaHoraFin->format('Y-m-d H:i:s'));
                $this->line("   ⏰ Límite (+ 1h): " . $tiempoLimite->format('Y-m-d H:i:s'));
                $this->line("   📊 Estatus: {$estatusActual}");
                
                if ($now->greaterThan($tiempoLimite)) {
                    if (in_array($estatusActual, ['Programado', 'Reprogramado'])) {
                        $this->error("   ❌ DEBE SER CANCELADO - Pasó el tiempo límite y está en estatus válido");
                    } else {
                        $this->info("   ✅ No se cancela - Estatus: {$estatusActual}");
                    }
                } else {
                    $minutosRestantes = $now->diffInMinutes($tiempoLimite);
                    $this->info("   ⏳ Faltan {$minutosRestantes} minutos para vencer");
                }
                $this->line("");
            }
        }

        return 0;
    }
}
