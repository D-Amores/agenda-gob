<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
