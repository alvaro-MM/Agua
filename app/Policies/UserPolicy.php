<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Permissions;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::for('ver', 'usuarios'));
    }

    public function view(User $user, User $model): bool
    {
        return $user->can(Permissions::for('ver', 'usuarios'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::for('crear', 'usuarios'));
    }

    public function update(User $user, User $model): bool
    {
        return $user->can(Permissions::for('editar', 'usuarios'));
    }

    /**
     * Dos salvaguardas además del permiso: nadie se borra a sí mismo (perdería
     * la sesión a media faena) y nunca se elimina al último administrador, que
     * dejaría el panel sin quien lo gestione.
     */
    public function delete(User $user, User $model): bool
    {
        if (! $user->can(Permissions::for('eliminar', 'usuarios'))) {
            return false;
        }

        if ($user->is($model)) {
            return false;
        }

        return ! $model->isLastAdmin();
    }
}
