<?php

namespace Tests\Feature\Contenido;

use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Support\PublicContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ContenidoModelosTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_url_resuelve_una_ruta_del_disco_publico(): void
    {
        $producto = Product::factory()->create(['image_path' => 'catalogo/bomba.webp']);

        $this->assertStringEndsWith('/storage/catalogo/bomba.webp', $producto->image_url);
    }

    public function test_image_url_deja_intacta_una_url_absoluta(): void
    {
        $url = 'https://ejemplo.test/foto.jpg';
        $proyecto = Project::factory()->create(['image_path' => $url]);

        $this->assertSame($url, $proyecto->image_url);
    }

    public function test_image_url_es_nulo_si_no_hay_imagen(): void
    {
        $this->assertNull(Product::factory()->create(['image_path' => null])->image_url);
    }

    public function test_los_scopes_filtran_borradores_y_papelera(): void
    {
        Service::factory()->create();
        Service::factory()->draft()->create();
        Service::factory()->create()->delete();

        $this->assertSame(1, Service::query()->published()->count());
    }

    public function test_guardar_contenido_invalida_la_cache_publica(): void
    {
        Service::factory()->create();

        $this->assertCount(1, PublicContent::services());
        $this->assertTrue(Cache::has(PublicContent::KEY_SERVICES));

        Service::factory()->create();

        $this->assertFalse(Cache::has(PublicContent::KEY_SERVICES));
        $this->assertCount(2, PublicContent::services());
    }

    public function test_restaurar_de_la_papelera_tambien_invalida_la_cache(): void
    {
        $servicio = Service::factory()->create();
        $servicio->delete();

        PublicContent::services();
        $this->assertTrue(Cache::has(PublicContent::KEY_SERVICES));

        $servicio->restore();

        $this->assertFalse(Cache::has(PublicContent::KEY_SERVICES));
    }
}
