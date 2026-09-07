<?php

namespace App\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Guarda una imagen subida desde el panel como WebP, redimensionada, y genera
 * una miniatura junto a ella.
 *
 * Evita el problema práctico de que Miguel suba una foto de 8 MB hecha con el
 * móvil y hunda el rendimiento de la web pública, sin depender de binarios
 * externos (cwebp, jpegoptim) instalados en el servidor.
 */
class StoreOptimizedImage
{
    /** Ancho máximo de la imagen principal, en píxeles. */
    public const MAX_WIDTH = 1600;

    /** Ancho de la miniatura, en píxeles. */
    public const THUMBNAIL_WIDTH = 400;

    public const QUALITY = 82;

    public const DISK = 'public';

    /**
     * @return string ruta relativa dentro del disco público, la que se guarda
     *                en la columna image_path
     */
    public function __invoke(UploadedFile $file, string $directory): string
    {
        $manager = new ImageManager(new Driver);
        $disk = Storage::disk(self::DISK);

        $name = Str::random(20);
        $path = "{$directory}/{$name}.webp";
        $thumbnailPath = self::thumbnailPath($path);

        $image = $manager->read($file->getRealPath());

        // scaleDown no amplía imágenes más pequeñas que el límite.
        $disk->put($path, (string) $image->scaleDown(width: self::MAX_WIDTH)->toWebp(self::QUALITY));
        $disk->put($thumbnailPath, (string) $image->scaleDown(width: self::THUMBNAIL_WIDTH)->toWebp(self::QUALITY));

        return $path;
    }

    /** Ruta de la miniatura asociada a una imagen ya guardada. */
    public static function thumbnailPath(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return Str::beforeLast($path, ".{$extension}")."_thumb.{$extension}";
    }
}
