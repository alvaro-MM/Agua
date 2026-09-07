<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use App\Support\Permissions;

class ServicePolicy
{
    private const RESOURCE = 'servicios';

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::for('ver', self::RESOURCE));
    }

    public function view(User $user, Service $model): bool
    {
        return $user->can(Permissions::for('ver', self::RESOURCE));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::for('crear', self::RESOURCE));
    }

    public function update(User $user, Service $model): bool
    {
        return $user->can(Permissions::for('editar', self::RESOURCE));
    }

    public function delete(User $user, Service $model): bool
    {
        return $user->can(Permissions::for('eliminar', self::RESOURCE));
    }

    public function restore(User $user, Service $model): bool
    {
        return $user->can(Permissions::for('eliminar', self::RESOURCE));
    }

    public function forceDelete(User $user, Service $model): bool
    {
        return $user->can(Permissions::for('eliminar', self::RESOURCE));
    }

    /** Publicar o destacar es una decisión editorial reservada al administrador. */
    public function publish(User $user): bool
    {
        return $user->can(Permissions::for('publicar', self::RESOURCE));
    }
}
