<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Punto único de lectura del contenido público, cacheado.
 *
 * La web pública es de solo lectura y cambia cuando Miguel guarda algo en el
 * panel, así que las consultas se cachean sin caducidad y se invalidan por
 * evento (ver el trait FlushesPublicCache).
 */
final class PublicContent
{
    public const KEY_SETTINGS = 'public.settings';

    public const KEY_SERVICES = 'public.services';

    public const KEY_SERVICES_FEATURED = 'public.services.featured';

    public const KEY_PRODUCTS = 'public.products';

    public const KEY_PROJECTS = 'public.projects';

    public const KEY_PROJECTS_FEATURED = 'public.projects.featured';

    /** Cuántos elementos destacados caben en la portada. */
    public const HOME_LIMIT = 3;

    public static function settings(): SiteSettings
    {
        return Cache::rememberForever(self::KEY_SETTINGS, fn (): SiteSettings => SiteSettings::firstOrNew([]));
    }

    /** @return Collection<int, Service> */
    public static function services(): Collection
    {
        return Cache::rememberForever(
            self::KEY_SERVICES,
            fn (): Collection => Service::query()->published()->orderBy('id')->get()
        );
    }

    /** @return Collection<int, Service> */
    public static function featuredServices(): Collection
    {
        return Cache::rememberForever(
            self::KEY_SERVICES_FEATURED,
            fn (): Collection => Service::query()->published()->featured()->orderBy('id')->limit(self::HOME_LIMIT)->get()
        );
    }

    /** @return Collection<int, Product> */
    public static function products(): Collection
    {
        return Cache::rememberForever(
            self::KEY_PRODUCTS,
            fn (): Collection => Product::query()->published()->orderBy('id')->get()
        );
    }

    /** @return Collection<int, Project> */
    public static function projects(): Collection
    {
        return Cache::rememberForever(
            self::KEY_PROJECTS,
            fn (): Collection => Project::query()->published()->orderBy('id')->get()
        );
    }

    /** @return Collection<int, Project> */
    public static function featuredProjects(): Collection
    {
        return Cache::rememberForever(
            self::KEY_PROJECTS_FEATURED,
            fn (): Collection => Project::query()->published()->featured()->orderBy('id')->limit(self::HOME_LIMIT)->get()
        );
    }

    /**
     * Se llama al guardar cualquier contenido desde el panel. Se vacían todas
     * las claves y no solo la del modelo tocado: son seis entradas baratas de
     * recalcular y así nunca queda nada obsoleto.
     */
    public static function flush(): void
    {
        foreach (self::keys() as $key) {
            Cache::forget($key);
        }
    }

    /** @return list<string> */
    public static function keys(): array
    {
        return [
            self::KEY_SETTINGS,
            self::KEY_SERVICES,
            self::KEY_SERVICES_FEATURED,
            self::KEY_PRODUCTS,
            self::KEY_PROJECTS,
            self::KEY_PROJECTS_FEATURED,
        ];
    }
}
