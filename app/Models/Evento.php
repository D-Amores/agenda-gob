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
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function vestimenta(){
        return $this->belongsTo(Vestimenta::class);
    }

    public function estatus()
    {
        return $this->belongsTo(Estatus::class);
    }

    //public function area(){
        //return $this->belongsTo(Area::class)->withDefault(); // opcional
    //}
}