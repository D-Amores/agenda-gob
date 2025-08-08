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
        'area_id',
        'estatus_id',
        'user_id',
    ];

    // Relaciones
    public function estatus()
    {
        return $this->belongsTo(Estatus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //public function area()
    //{
        //return $this->belongsTo(Area::class);
    //}
}
