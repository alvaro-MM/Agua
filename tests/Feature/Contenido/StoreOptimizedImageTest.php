<?php

namespace Tests\Feature\Contenido;

use App\Actions\StoreOptimizedImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class StoreOptimizedImageTest extends TestCase
{
    public function test_convierte_a_webp_y_genera_miniatura(): void
    {
        Storage::fake(StoreOptimizedImage::DISK);

        $original = UploadedFile::fake()->image('foto-del-movil.jpg', width: 4000, height: 3000);

        $path = app(StoreOptimizedImage::class)($original, 'catalogo');

        $disk = Storage::disk(StoreOptimizedImage::DISK);

        $this->assertStringStartsWith('catalogo/', $path);
        $this->assertStringEndsWith('.webp', $path);
        $disk->assertExists($path);
        $disk->assertExists(StoreOptimizedImage::thumbnailPath($path));

        $manager = new ImageManager(new Driver);

        $imagen = $manager->read($disk->get($path));
        $miniatura = $manager->read($disk->get(StoreOptimizedImage::thumbnailPath($path)));

        $this->assertSame(StoreOptimizedImage::MAX_WIDTH, $imagen->width());
        $this->assertSame(StoreOptimizedImage::THUMBNAIL_WIDTH, $miniatura->width());

        // La proporción original (4:3) se respeta.
        $this->assertSame(1200, $imagen->height());
    }

    public function test_no_amplia_una_imagen_mas_pequena_que_el_limite(): void
    {
        Storage::fake(StoreOptimizedImage::DISK);

        $path = app(StoreOptimizedImage::class)(
            UploadedFile::fake()->image('pequena.png', width: 500, height: 500),
            'proyectos'
        );

        $imagen = (new ImageManager(new Driver))->read(Storage::disk(StoreOptimizedImage::DISK)->get($path));

        $this->assertSame(500, $imagen->width());
    }

    public function test_la_ruta_de_la_miniatura_se_deriva_de_la_imagen(): void
    {
        $this->assertSame(
            'catalogo/abc123_thumb.webp',
            StoreOptimizedImage::thumbnailPath('catalogo/abc123.webp')
        );
    }
}
