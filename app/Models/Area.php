<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'c_area';

    protected $fillable = [
        'area',
        'responsable'
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class); // asumiendo que el modelo User tiene area_id
    }

    public function audiencias()
    {
        return $this->hasMany(Audiencia::class); // asumiendo que Audiencia tiene area_id
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class); // asumiendo que Audiencia tiene area_id
    }
}
