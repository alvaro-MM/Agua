<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * `image_path` admite dos formas: una ruta relativa en el disco público (lo
 * que sube Miguel desde el panel) o una URL absoluta (las imágenes de ejemplo
 * con las que se siembra el sitio). El accessor resuelve ambas.
 */
trait HasImagePath
{
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $path = $this->image_path;

            if (blank($path)) {
                return null;
            }

            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }

            return Storage::disk('public')->url($path);
        });
    }
}
