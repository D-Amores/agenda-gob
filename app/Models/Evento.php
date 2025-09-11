<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';
    protected $fillable = [
        'nombre',
        'lugar',
        'descripcion',
        'asistencia_de_gobernador',
        'fecha_evento',
        'hora_evento',
        'hora_fin_evento',
        'user_id',
        'vestimenta_id',
        'estatus_id',
        'area_id',
    ];

    public function isEventoDuplicated($validated, $areaId, $eventoId = null)
    {
        $query = self::where('area_id', $areaId)
            ->whereDate('fecha_evento', $validated['formValidationFecha'])
            ->where(function ($q) use ($validated) {
                // Inicio dentro de un rango existente
                $q->whereBetween('hora_evento', [$validated['hora_evento'], $validated['hora_fin_evento']])
                    // Fin dentro de un rango existente
                    ->orWhereBetween('hora_fin_evento', [$validated['hora_evento'], $validated['hora_fin_evento']])
                    // Rango nuevo cubre un rango existente
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('hora_evento', '<=', $validated['hora_evento'])
                            ->where('hora_fin_evento', '>=', $validated['hora_fin_evento']);
                    });
            })
            ->where('nombre', $validated['formValidationName']); // mismo nombre

        if ($eventoId) {
            $query->where('id', '!=', $eventoId); // excluir evento actual al actualizar
        }

        return $query->exists(); // true si hay conflicto
    }

    public static function isEventoPast($fechaEvento, $horaFinEvento): bool
    {
        if (empty($fechaEvento) || empty($horaFinEvento)) {
            return false; // evita errores si faltan datos
        }

        try {
            $fechaHoraFin = Carbon::parse("{$fechaEvento} {$horaFinEvento}");
            return $fechaHoraFin->isPast();
        } catch (\Exception $e) {
            return false; // en caso de fecha/hora inválida
        }
    }

    /**
     * Verifica si el evento debe ser marcado como cancelado automáticamente
     * (pasó 1 hora después de su finalización y sigue en estatus programado/reprogramado)
     */
    public function debeSerCancelado(): bool
    {
        try {
            $now = Carbon::now();
            $horaFinalizacion = Carbon::parse("{$this->fecha_evento} {$this->hora_fin_evento}");
            $tiempoLimite = $horaFinalizacion->addHour();

            // Solo si está en estatus programado o reprogramado
            $estatusValidos = ['Programado', 'Reprogramado'];
            $estatusActual = $this->estatus->estatus ?? '';

            return $now->greaterThan($tiempoLimite) && in_array($estatusActual, $estatusValidos);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vestimenta()
    {
        return $this->belongsTo(Vestimenta::class);
    }

    public function estatus()
    {
        return $this->belongsTo(Estatus::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class)->withDefault(); // opcional
    }
}
