<?php

namespace Tests\Feature\Panel;

use App\Actions\StoreOptimizedImage;
use App\Filament\Pages\SiteSettingsPage;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Services\Pages\EditService;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSettings;
use App\Models\User;
use App\Support\Permissions;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ContenidoPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function admin(): User
    {
        return tap(User::factory()->create())->assignRole(Permissions::ROLE_ADMIN);
    }

    private function tecnico(): User
    {
        return tap(User::factory()->create())->assignRole(Permissions::ROLE_TECNICO);
    }

    public function test_un_administrador_ve_los_tres_listados_de_contenido(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/services')->assertOk();
        $this->get('/admin/products')->assertOk();
        $this->get('/admin/projects')->assertOk();
    }

    public function test_un_tecnico_consulta_el_contenido_pero_no_lo_crea(): void
    {
        $tecnico = $this->tecnico();

        $this->actingAs($tecnico)->get('/admin/services')->assertOk();

        $this->assertFalse($tecnico->can('create', Service::class));
        $this->assertFalse($tecnico->can('update', Service::factory()->create()));
        $this->assertFalse($tecnico->can('delete', Service::factory()->create()));
        $this->assertFalse($tecnico->can('publish', Service::class));
    }

    public function test_un_administrador_puede_publicar(): void
    {
        $this->assertTrue($this->admin()->can('publish', Service::class));
    }

    public function test_crear_un_producto_genera_el_slug_y_lo_publica(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Bomba de prueba',
                'slug' => 'bomba-de-prueba',
                'category' => 'Bombas',
                'description' => 'Una bomba para el test.',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $producto = Product::where('slug', 'bomba-de-prueba')->sole();

        $this->assertSame('Bombas', $producto->category);
        $this->assertTrue($producto->is_published);
    }

    public function test_editar_un_servicio_conserva_sus_puntos(): void
    {
        $this->actingAs($this->admin());

        $servicio = Service::factory()->create(['features' => ['Uno', 'Dos']]);

        Livewire::test(EditService::class, ['record' => $servicio->getRouteKey()])
            ->fillForm(['title' => 'Título nuevo'])
            ->call('save')
            ->assertHasNoFormErrors();

        $servicio->refresh();

        $this->assertSame('Título nuevo', $servicio->title);
        $this->assertSame(['Uno', 'Dos'], $servicio->features);
    }

    public function test_editar_un_producto_no_borra_una_imagen_externa(): void
    {
        $this->actingAs($this->admin());

        // Así llegan las imágenes del seeder: URL absoluta, no un fichero del
        // disco. Filament las descartaría por no existir en storage.
        $url = 'https://loremflickr.com/800/600/water,pump?lock=11';
        $producto = Product::factory()->create(['image_path' => $url]);

        Livewire::test(EditProduct::class, ['record' => $producto->getRouteKey()])
            ->fillForm(['name' => 'Nombre nuevo'])
            ->call('save')
            ->assertHasNoFormErrors();

        $producto->refresh();

        $this->assertSame('Nombre nuevo', $producto->name);
        $this->assertSame($url, $producto->image_path);
    }

    public function test_subir_una_imagen_la_guarda_optimizada(): void
    {
        Storage::fake(StoreOptimizedImage::DISK);

        $this->actingAs($this->admin());

        $producto = Product::factory()->create(['image_path' => null]);

        Livewire::test(EditProduct::class, ['record' => $producto->getRouteKey()])
            ->fillForm(['image_path' => [UploadedFile::fake()->image('bomba.jpg', 2400, 1600)]])
            ->call('save')
            ->assertHasNoFormErrors();

        $producto->refresh();

        $this->assertStringStartsWith('catalogo/', $producto->image_path);
        $this->assertStringEndsWith('.webp', $producto->image_path);
        Storage::disk(StoreOptimizedImage::DISK)->assertExists($producto->image_path);
        Storage::disk(StoreOptimizedImage::DISK)->assertExists(
            StoreOptimizedImage::thumbnailPath($producto->image_path)
        );
    }

    public function test_solo_el_administrador_entra_en_los_ajustes_del_sitio(): void
    {
        $this->actingAs($this->tecnico())->get('/admin/ajustes')->assertForbidden();
        $this->actingAs($this->admin())->get('/admin/ajustes')->assertOk();
    }

    public function test_guardar_los_ajustes_actualiza_la_fila_unica(): void
    {
        $this->actingAs($this->admin());

        SiteSettings::current()->update(['company_name' => 'Antiguo', 'phone' => '+34 600 000 000']);

        Livewire::test(SiteSettingsPage::class)
            ->fillForm([
                'company_name' => 'Electro Bombas MAPF',
                'phone' => '+34 611 222 333',
                'service_areas' => ['Murcia', 'Cartagena'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $ajustes = SiteSettings::current();

        $this->assertSame('Electro Bombas MAPF', $ajustes->company_name);
        $this->assertSame('+34 611 222 333', $ajustes->phone);
        $this->assertSame(['Murcia', 'Cartagena'], $ajustes->service_areas);
        $this->assertSame(1, SiteSettings::query()->count());
    }
}
