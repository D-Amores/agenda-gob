<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audiencia extends Model
{
    use HasFactory;

    protected $table = 'audiencias';

    protected $fillable = [
        'nombre',
        'lugar',
        'asunto_audiencia',
        'descripcion',
        'procedencia',
        'fecha_audiencia',
        'hora_audiencia',
        'hora_fin_audiencia',
        'area_id',
        'estatus_id',
        'user_id',
    ];

    public function isAudienciaDuplicated($validated, $areaId, $audienciaId = null)
    {
        $query = self::where('area_id', $areaId)
            ->whereDate('fecha_audiencia', $validated['formValidationFecha'])
            ->where(function ($q) use ($validated) {
                // Inicio dentro del rango existente
                $q->whereBetween('hora_audiencia', [$validated['hora_audiencia'], $validated['hora_fin_audiencia']])
                    // Fin dentro del rango existente
                    ->orWhereBetween('hora_fin_audiencia', [$validated['hora_audiencia'], $validated['hora_fin_audiencia']])
                    // Rango nuevo cubre rango existente
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('hora_audiencia', '<=', $validated['hora_audiencia'])
                            ->where('hora_fin_audiencia', '>=', $validated['hora_fin_audiencia']);
                    });
            })
            ->where('nombre', $validated['formValidationName'])
            ->where('asunto_audiencia', $validated['formValidationAsunto']);

        if ($audienciaId) {
            $query->where('id', '!=', $audienciaId);
        }

        return $query->exists(); // true si hay conflicto, false si no
    }


    // Relaciones
    public function estatus()
    {
        return $this->belongsTo(Estatus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
