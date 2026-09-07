<?php

namespace Tests\Feature\Contenido;

use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSettings;
use App\Support\PublicContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * La suite corre con el driver `array`, que guarda el objeto vivo y por tanto
 * no detecta problemas de serialización. En producción el driver es
 * `database`, que serializa; y Laravel trae cache.serializable_classes en
 * false, así que ninguna clase se rehidrata desde la caché. Estos tests
 * ejercitan ese camino de verdad.
 */
class CachePublicaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Driver que serializa, como en producción.
        config(['cache.default' => 'database']);
        Cache::purge('database');
    }

    public function test_los_ajustes_sobreviven_a_un_driver_que_serializa(): void
    {
        SiteSettings::current()->update([
            'company_name' => 'Electro Bombas MAPF',
            'phone' => '+34 611 222 333',
            'service_areas' => ['Murcia', 'Cartagena'],
            'founded_year' => 2015,
        ]);

        // Primera lectura: consulta y cachea.
        PublicContent::settings();

        // Segunda lectura: sale de la caché ya serializada.
        $ajustes = PublicContent::settings();

        $this->assertInstanceOf(SiteSettings::class, $ajustes);
        $this->assertSame('Electro Bombas MAPF', $ajustes->company_name);
        $this->assertSame('+34 611 222 333', $ajustes->phone);
        // Los casts se aplican tras rehidratar.
        $this->assertSame(['Murcia', 'Cartagena'], $ajustes->service_areas);
        $this->assertSame(2015, $ajustes->founded_year);
    }

    public function test_las_colecciones_sobreviven_a_un_driver_que_serializa(): void
    {
        Service::factory()->create(['title' => 'Instalación', 'features' => ['Uno', 'Dos']]);

        PublicContent::services();
        $servicios = PublicContent::services();

        $this->assertCount(1, $servicios);
        $this->assertInstanceOf(Service::class, $servicios->first());
        $this->assertSame('Instalación', $servicios->first()->title);
        $this->assertSame(['Uno', 'Dos'], $servicios->first()->features);
        $this->assertTrue($servicios->first()->is_published);
        $this->assertTrue($servicios->first()->exists);
    }

    public function test_los_accessors_funcionan_tras_rehidratar(): void
    {
        Product::factory()->create(['image_path' => 'catalogo/bomba.webp']);

        PublicContent::products();

        $this->assertStringEndsWith('/storage/catalogo/bomba.webp', PublicContent::products()->first()->image_url);
    }

    public function test_sin_fila_de_ajustes_devuelve_un_modelo_vacio(): void
    {
        $ajustes = PublicContent::settings();

        $this->assertInstanceOf(SiteSettings::class, $ajustes);
        $this->assertNull($ajustes->company_name);
    }

    public function test_guardar_invalida_la_cache_tambien_con_este_driver(): void
    {
        SiteSettings::current()->update(['phone' => '+34 600 000 000']);

        $this->assertSame('+34 600 000 000', PublicContent::settings()->phone);

        SiteSettings::current()->update(['phone' => '+34 611 222 333']);

        $this->assertSame('+34 611 222 333', PublicContent::settings()->phone);
    }
}
