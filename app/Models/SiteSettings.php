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

    /**
     * Datos estructurados para Google (JSON-LD).
     *
     * Se genera aquí y no en la plantilla a propósito: Blade tiene una
     * directiva @context que se comía la clave '@context' del array y dejaba
     * PHP crudo dentro del <script>, rompiendo la ficha del negocio.
     */
    public function toSchemaOrg(): string
    {
        return (string) json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Plumber',
            'name' => $this->company_name,
            'description' => $this->description,
            'url' => url('/'),
            'telephone' => $this->phone,
            'email' => $this->email,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->address,
                'postalCode' => $this->postal_code,
                'addressLocality' => $this->city,
                'addressCountry' => 'ES',
            ],
            'areaServed' => $this->service_areas ?? [],
            'openingHours' => 'Mo-Fr 08:00-18:00',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
