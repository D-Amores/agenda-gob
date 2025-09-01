<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    use HasFactory;
    
    protected $table = 'estatus';
    protected $fillable = ['estatus'];

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    public function audiencias()
    {
        return $this->hasMany(Audiencia::class);
    }
}
