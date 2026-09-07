<?php

namespace App\Concerns;

use App\Support\PublicContent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Vacía la caché de la web pública cuando el contenido cambia desde el panel,
 * para que Miguel vea el efecto de lo que guarda sin tocar la consola.
 */
trait FlushesPublicCache
{
    public static function bootFlushesPublicCache(): void
    {
        $flush = static fn () => PublicContent::flush();

        static::saved($flush);
        static::deleted($flush);

        // Los ajustes del sitio no tienen papelera; el resto del contenido sí.
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), strict: true)) {
            static::restored($flush);
            static::forceDeleted($flush);
        }
    }
}
