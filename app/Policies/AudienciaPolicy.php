<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Audiencia;

class AudienciaPolicy
{
    /**
     * Ver si el usuario puede ver una audiencia
     */
    public function view(User $user, Audiencia $audiencia): bool
    {
        return $user->area_id === $audiencia->area_id && $user->can('ver audiencia');
    }

    /**
     * Ver si el usuario puede crear una audiencia
     */
    public function create(User $user): bool
    {
        return $user->can('crear audiencia');
    }

    /**
     * Ver si el usuario puede actualizar una audiencia
     */
    public function update(User $user, Audiencia $audiencia): bool
    {
        return $user->id === $audiencia->user_id && $user->can('editar audiencia');
    }

    /**
     * Ver si el usuario puede eliminar una audiencia
     */
    public function delete(User $user, Audiencia $audiencia): bool
    {
        return $user->id === $audiencia->user_id && $user->can('eliminar audiencia');
    }
}
