<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vestimenta extends Model
{
    use HasFactory;

    protected $fillable = ['tipo'];

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }


}
