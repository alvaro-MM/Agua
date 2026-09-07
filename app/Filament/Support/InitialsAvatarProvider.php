<?php

namespace App\Filament\Support;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Avatar con las iniciales, generado como SVG en el propio servidor.
 *
 * Filament los pide por defecto a ui-avatars.com: eso deja la imagen rota si
 * el servidor no tiene salida a internet y, de paso, envía los nombres de los
 * usuarios a un tercero en cada carga de página.
 */
class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        $initials = $this->initials(Filament::getNameForDefaultAvatar($record));
        $background = Color::convertToHex(FilamentColor::getColor('primary')[600] ?? Color::Sky[600]);

        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect width="100" height="100" fill="{$background}"/>
            <text x="50" y="50" dy="0.35em" fill="#FFFFFF" text-anchor="middle"
                  font-family="system-ui, sans-serif" font-size="42" font-weight="600">{$initials}</text>
        </svg>
        SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /** Hasta dos iniciales, saltándose la puntuación inicial de cada palabra. */
    private function initials(string $name): string
    {
        $letters = str($name)
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => mb_substr(
                (string) preg_replace('/^[^\p{L}\p{N}]+/u', '', $segment), 0, 1
            ))
            ->filter()
            ->take(2)
            ->join('');

        return e(mb_strtoupper($letters)) ?: '?';
    }
}
