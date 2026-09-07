<?php

namespace Tests\Feature\Contenido;

use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_vuelca_el_contenido_de_config_site_a_la_base_de_datos(): void
    {
        $this->seed(SiteContentSeeder::class);

        $this->assertCount(count(config('site.services')), Service::all());
        $this->assertCount(count(config('site.catalog')), Product::all());
        $this->assertCount(count(config('site.projects')), Project::all());

        $ajustes = SiteSettings::current();

        $this->assertSame(config('site.company.name'), $ajustes->company_name);
        $this->assertSame(config('site.contact.phone'), $ajustes->phone);
        $this->assertSame(config('site.contact.notify_email'), $ajustes->notify_email);
        $this->assertSame(config('site.company.service_areas'), $ajustes->service_areas);
    }

    public function test_conserva_el_orden_y_los_iconos_originales(): void
    {
        $this->seed(SiteContentSeeder::class);

        $slugs = Service::query()->orderBy('id')->pluck('slug')->all();

        $this->assertSame(array_column(config('site.services'), 'slug'), $slugs);

        $primero = Service::query()->orderBy('id')->first();

        $this->assertSame(config('site.services.0.icon'), $primero->icon);
        $this->assertSame(config('site.services.0.features'), $primero->features);
    }

    public function test_es_idempotente(): void
    {
        $this->seed(SiteContentSeeder::class);
        $this->seed(SiteContentSeeder::class);

        $this->assertCount(count(config('site.services')), Service::all());
        $this->assertCount(count(config('site.catalog')), Product::all());
        $this->assertSame(1, SiteSettings::query()->count());
    }

    public function test_los_destacados_de_portada_quedan_marcados(): void
    {
        $this->seed(SiteContentSeeder::class);

        $this->assertGreaterThan(0, Service::query()->featured()->count());
        $this->assertGreaterThan(0, Project::query()->featured()->count());
    }
}
