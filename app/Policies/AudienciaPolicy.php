<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Audiencia;

class AudienciaPolicy
{
    public function view(User $user, Audiencia $audiencia): bool
    {
        return $user->area_id === $audiencia->area_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Audiencia $audiencia): bool
    {
        return $user->id === $audiencia->user_id;
    }

    public function delete(User $user, Audiencia $audiencia): bool
    {
        return $user->id === $audiencia->user_id;
    }
}
