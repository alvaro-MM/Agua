<?php

namespace Tests\Feature\Public;

use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use App\Support\PublicContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PaginasPublicasTest extends TestCase
{
    use RefreshDatabase;

    private function ajustes(array $overrides = []): SiteSettings
    {
        return tap(SiteSettings::current())->update(array_merge([
            'company_name' => 'Electro Bombas MAPF',
            'legal_name' => 'Electro Bombas MAPF SL',
            'nif' => 'B12345678',
            'tagline' => 'Bombas de agua',
            'description' => 'Instalación y reparación de bombas.',
            'founded_year' => 2015,
            'city' => 'Murcia',
            'service_areas' => ['Murcia', 'Cartagena'],
            'phone' => '+34 611 222 333',
            'phone_link' => '+34611222333',
            'whatsapp' => '34611222333',
            'whatsapp_message' => 'Hola',
            'email' => 'info@electrobombas.test',
            'notify_email' => 'avisos@electrobombas.test',
            'address' => 'Calle Mayor, 1',
            'postal_code' => '30001',
            'schedule' => 'Lunes a viernes',
            'schedule_short' => 'L-V 8-18',
        ], $overrides));
    }

    /** @return array<string, array{string}> */
    public static function rutasPublicas(): array
    {
        return [
            'inicio' => ['home'],
            'servicios' => ['services'],
            'catálogo' => ['catalog'],
            'proyectos' => ['projects'],
            'sobre nosotros' => ['about'],
            'contacto' => ['contact'],
            'aviso legal' => ['legal.notice'],
            'privacidad' => ['legal.privacy'],
            'sitemap' => ['sitemap'],
        ];
    }

    #[DataProvider('rutasPublicas')]
    public function test_cada_pagina_publica_responde(string $ruta): void
    {
        $this->ajustes();
        Service::factory()->featured()->create();
        Product::factory()->create();
        Project::factory()->featured()->create();

        $this->get(route($ruta))->assertOk();
    }

    public function test_las_paginas_muestran_los_ajustes_guardados(): void
    {
        $this->ajustes(['company_name' => 'Bombas de Prueba', 'phone' => '+34 999 888 777']);

        $this->get(route('home'))
            ->assertSee('Bombas de Prueba')
            ->assertSee('+34 999 888 777');

        // El aviso legal usa la razón social y el NIF.
        $this->get(route('legal.notice'))
            ->assertSee('Electro Bombas MAPF SL')
            ->assertSee('B12345678');
    }

    public function test_cambiar_un_ajuste_se_ve_en_la_web_sin_limpiar_cache(): void
    {
        $ajustes = $this->ajustes(['phone' => '+34 600 000 000']);

        $this->get(route('home'))->assertSee('+34 600 000 000');

        $ajustes->update(['phone' => '+34 611 222 333']);

        $this->get(route('home'))
            ->assertSee('+34 611 222 333')
            ->assertDontSee('+34 600 000 000');
    }

    public function test_el_contenido_en_borrador_no_aparece(): void
    {
        $this->ajustes();

        $publicado = Service::factory()->create(['title' => 'Servicio visible']);
        Service::factory()->draft()->create(['title' => 'Servicio en borrador']);

        $this->get(route('services'))
            ->assertSee($publicado->title)
            ->assertDontSee('Servicio en borrador');
    }

    public function test_el_contenido_en_la_papelera_no_aparece(): void
    {
        $this->ajustes();

        $producto = Product::factory()->create(['name' => 'Bomba retirada']);
        Product::factory()->create(['name' => 'Bomba a la venta']);

        $producto->delete();

        $this->get(route('catalog'))
            ->assertSee('Bomba a la venta')
            ->assertDontSee('Bomba retirada');
    }

    public function test_la_portada_solo_muestra_lo_destacado(): void
    {
        $this->ajustes();

        Service::factory()->featured()->create(['title' => 'Servicio destacado']);
        Service::factory()->create(['title' => 'Servicio secundario']);
        Project::factory()->featured()->create(['title' => 'Proyecto destacado']);
        Project::factory()->create(['title' => 'Proyecto secundario']);

        $this->get(route('home'))
            ->assertSee('Servicio destacado')
            ->assertDontSee('Servicio secundario')
            ->assertSee('Proyecto destacado')
            ->assertDontSee('Proyecto secundario');
    }

    public function test_el_catalogo_agrupa_por_categoria(): void
    {
        $this->ajustes();

        Product::factory()->create(['category' => 'Bombas', 'name' => 'Sumergible']);
        Product::factory()->create(['category' => 'Accesorios', 'name' => 'Presostato']);

        $this->get(route('catalog'))
            ->assertSee('Bombas')
            ->assertSee('Accesorios')
            ->assertSee('Sumergible')
            ->assertSee('Presostato');
    }

    public function test_los_datos_estructurados_son_json_valido(): void
    {
        $this->ajustes();

        $html = $this->get(route('home'))->assertOk()->getContent();

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $coincidencias);

        $this->assertNotEmpty($coincidencias, 'La portada no incluye datos estructurados.');

        $datos = json_decode(trim($coincidencias[1]), associative: true);

        $this->assertIsArray($datos, 'Los datos estructurados no son JSON válido.');
        $this->assertSame('https://schema.org', $datos['@context'] ?? null);
        $this->assertSame('Plumber', $datos['@type']);
        $this->assertSame('Electro Bombas MAPF', $datos['name']);
        $this->assertSame('+34 611 222 333', $datos['telephone']);
        $this->assertSame('Murcia', $datos['address']['addressLocality']);
        $this->assertSame(['Murcia', 'Cartagena'], $datos['areaServed']);
    }

    public function test_la_portada_limita_los_destacados(): void
    {
        $this->ajustes();

        Project::factory()->featured()->count(PublicContent::HOME_LIMIT + 2)->create();

        $this->assertCount(PublicContent::HOME_LIMIT, PublicContent::featuredProjects());
    }
}
