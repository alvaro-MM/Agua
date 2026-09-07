<?php

namespace App\Models;

use App\Support\Permissions;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Roles con acceso al panel de gestión.
     *
     * @var list<string>
     */
    public const PANEL_ROLES = [Permissions::ROLE_ADMIN, Permissions::ROLE_TECNICO];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Solo los usuarios con un rol del negocio entran al panel. Un usuario sin
     * rol existe en la base de datos pero no puede acceder a /admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(self::PANEL_ROLES);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Permissions::ROLE_ADMIN);
    }

    /**
     * ¿Es el único administrador que queda? Se usa para impedir que el panel
     * se quede sin nadie capaz de gestionarlo.
     */
    public function isLastAdmin(): bool
    {
        return $this->isAdmin()
            && static::query()->role(Permissions::ROLE_ADMIN)->whereKeyNot($this->getKey())->doesntExist();
    }
}
