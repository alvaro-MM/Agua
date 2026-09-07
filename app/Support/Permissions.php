<?php

namespace App\Support;

/**
 * Catálogo único de permisos y roles del panel.
 *
 * Vive aquí (y no solo en el seeder) porque las Policies y los tests necesitan
 * referirse a los mismos nombres sin duplicar literales por el código.
 */
final class Permissions
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_TECNICO = 'tecnico';

    /** Recursos que tienen el juego completo de permisos CRUD. */
    public const RESOURCES = ['servicios', 'productos', 'proyectos', 'mensajes', 'usuarios'];

    /** Acciones aplicadas a cada recurso. */
    public const ACTIONS = ['ver', 'crear', 'editar', 'eliminar', 'publicar'];

    public const AJUSTES = 'gestionar ajustes';

    /**
     * Todos los permisos existentes.
     *
     * @return list<string>
     */
    public static function all(): array
    {
        $permissions = [];

        foreach (self::RESOURCES as $resource) {
            foreach (self::ACTIONS as $action) {
                $permissions[] = self::for($action, $resource);
            }
        }

        $permissions[] = self::AJUSTES;

        return $permissions;
    }

    /**
     * Permisos del rol técnico: consulta el contenido y trabaja la bandeja de
     * mensajes, pero no publica, no borra, no toca usuarios ni ajustes.
     *
     * @return list<string>
     */
    public static function forTecnico(): array
    {
        return [
            self::for('ver', 'servicios'),
            self::for('ver', 'productos'),
            self::for('ver', 'proyectos'),
            self::for('ver', 'mensajes'),
            self::for('editar', 'mensajes'),
        ];
    }

    public static function for(string $action, string $resource): string
    {
        return "{$action} {$resource}";
    }
}
