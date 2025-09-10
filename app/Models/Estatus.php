<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    use HasFactory;
    
    protected $table = 'estatus';
    protected $fillable = ['estatus'];

    public static function statusProgramado()
    {
        return self::whereRaw("LOWER(estatus) = 'programado'")->first();
    }

    /**
     * Devuelve todos los estatus excepto 'programado'
     */
    public static function statusWithOutProgramado()
    {
        return self::whereRaw("LOWER(estatus) != 'programado'")->get();
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    public function audiencias()
    {
        return $this->hasMany(Audiencia::class);
    }
}
