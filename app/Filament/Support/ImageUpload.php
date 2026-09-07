<?php

namespace App\Filament\Support;

use App\Actions\StoreOptimizedImage;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Campo de imagen compartido por catálogo y proyectos.
 *
 * Lo que sube Miguel se convierte a WebP y se acompaña de una miniatura (ver
 * StoreOptimizedImage). Además tolera que el valor guardado sea una URL
 * absoluta, que es como llegan las imágenes de ejemplo del seeder: sin esto,
 * Filament las descartaría al no encontrarlas en el disco y editar cualquier
 * otro campo del registro borraría la imagen.
 */
final class ImageUpload
{
    /** Tamaño máximo aceptado, en kilobytes. */
    public const MAX_SIZE_KB = 8192;

    public static function make(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->label('Imagen')
            ->image()
            ->imageEditor()
            ->disk(StoreOptimizedImage::DISK)
            ->directory($directory)
            ->maxSize(self::MAX_SIZE_KB)
            ->helperText('Se guardará optimizada en WebP. Máximo '.(self::MAX_SIZE_KB / 1024).' MB.')
            // Sin comprobar existencia en disco: admitimos URLs absolutas.
            ->fetchFileInformation(false)
            ->getUploadedFileUsing(function (BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array {
                if (Str::startsWith($file, ['http://', 'https://'])) {
                    return [
                        'name' => basename((string) parse_url($file, PHP_URL_PATH)) ?: $file,
                        'size' => 0,
                        'type' => null,
                        'url' => $file,
                    ];
                }

                return $component->getUploadedFile($file, $storedFileNames);
            })
            ->saveUploadedFileUsing(
                fn (TemporaryUploadedFile $file): string => app(StoreOptimizedImage::class)($file, $directory)
            );
    }
}
