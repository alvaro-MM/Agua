<?php

namespace Database\Seeders;

use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Crea (o actualiza) los roles del negocio. Es idempotente: se puede
     * relanzar tras añadir permisos nuevos sin duplicar nada.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permissions::all() as $name) {
            Permission::findOrCreate($name, 'web');
        }

        // Segunda limpieza: syncPermissions() resuelve los nombres contra la
        // caché del registrar, que se repobló antes de crear los permisos.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Miguel: acceso total.
        Role::findOrCreate(Permissions::ROLE_ADMIN, 'web')
            ->syncPermissions(Permissions::all());

        // Empleados: consulta y gestión de la bandeja de mensajes.
        Role::findOrCreate(Permissions::ROLE_TECNICO, 'web')
            ->syncPermissions(Permissions::forTecnico());
    }
}
