<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;
use App\Support\Permissions;

class ContactMessagePolicy
{
    private const RESOURCE = 'mensajes';

    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::for('ver', self::RESOURCE));
    }

    public function view(User $user, ContactMessage $message): bool
    {
        return $user->can(Permissions::for('ver', self::RESOURCE));
    }

    /** Los mensajes llegan del formulario público; nadie los crea a mano. */
    public function create(User $user): bool
    {
        return false;
    }

    /** Cambiar el estado y anotar es justo lo que hace un técnico. */
    public function update(User $user, ContactMessage $message): bool
    {
        return $user->can(Permissions::for('editar', self::RESOURCE));
    }

    /** Borrar un lead sí es cosa del administrador. */
    public function delete(User $user, ContactMessage $message): bool
    {
        return $user->can(Permissions::for('eliminar', self::RESOURCE));
    }

    public function restore(User $user, ContactMessage $message): bool
    {
        return $user->can(Permissions::for('eliminar', self::RESOURCE));
    }

    public function forceDelete(User $user, ContactMessage $message): bool
    {
        return $user->can(Permissions::for('eliminar', self::RESOURCE));
    }
}
