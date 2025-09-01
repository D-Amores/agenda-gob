<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PendingRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'email',
        'area_id',
        'verification_token',
        'password',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Generar token de verificación único
     */
    public static function generateVerificationToken()
    {
        return hash('sha256', Str::random(64) . now()->timestamp . uniqid());
    }

    /**
     * Verificar si el registro ha expirado
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Relación con el área
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Scope para registros no expirados
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope para registros expirados
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}
