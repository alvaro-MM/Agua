<?php

namespace App\Models;

use App\Concerns\FlushesPublicCache;
use Illuminate\Database\Eloquent\Model;

/**
 * Datos de la empresa, contacto y redes. Una única fila: el sitio es uno.
 *
 * Antes vivían en config/site.php y cambiarlos exigía un despliegue; ahora los
 * edita Miguel desde /admin.
 */
class SiteSettings extends Model
{
    use FlushesPublicCache;

    protected $table = 'site_settings';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'service_areas' => 'array',
            'founded_year' => 'integer',
        ];
    }

    /**
     * La fila de ajustes, creándola vacía si aún no existe. Para lectura en la
     * web pública usa PublicContent::settings(), que además la cachea.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
