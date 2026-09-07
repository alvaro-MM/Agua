<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Punto único de lectura del contenido público, cacheado.
 *
 * La web pública es de solo lectura y sólo cambia cuando Miguel guarda algo en
 * el panel, así que las consultas se cachean sin caducidad y se invalidan por
 * evento (ver el trait FlushesPublicCache).
 *
 * Importante: en la caché se guardan arrays de atributos, nunca los modelos.
 * Laravel trae `cache.serializable_classes => false` por defecto (protege
 * frente a cadenas de gadgets si se filtra la APP_KEY), de modo que cualquier
 * driver que serialice —database, file, redis— devolvería un
 * __PHP_Incomplete_Class si guardáramos objetos. Se rehidratan al leer.
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
        $attributes = Cache::rememberForever(
            self::KEY_SETTINGS,
            fn (): array => SiteSettings::query()->first()?->getAttributes() ?? []
        );

        return (new SiteSettings)->newFromBuilder($attributes);
    }

    /** @return Collection<int, Service> */
    public static function services(): Collection
    {
        return self::remember(self::KEY_SERVICES, Service::class, Service::query()->published()->orderBy('id'));
    }

    /** @return Collection<int, Service> */
    public static function featuredServices(): Collection
    {
        return self::remember(
            self::KEY_SERVICES_FEATURED,
            Service::class,
            Service::query()->published()->featured()->orderBy('id')->limit(self::HOME_LIMIT)
        );
    }

    /** @return Collection<int, Product> */
    public static function products(): Collection
    {
        return self::remember(self::KEY_PRODUCTS, Product::class, Product::query()->published()->orderBy('id'));
    }

    /** @return Collection<int, Project> */
    public static function projects(): Collection
    {
        return self::remember(self::KEY_PROJECTS, Project::class, Project::query()->published()->orderBy('id'));
    }

    /** @return Collection<int, Project> */
    public static function featuredProjects(): Collection
    {
        return self::remember(
            self::KEY_PROJECTS_FEATURED,
            Project::class,
            Project::query()->published()->featured()->orderBy('id')->limit(self::HOME_LIMIT)
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

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $model
     * @param  Builder<TModel>  $query
     * @return Collection<int, TModel>
     */
    private static function remember(string $key, string $model, $query): Collection
    {
        $rows = Cache::rememberForever(
            $key,
            fn (): array => $query->get()->map(fn (Model $record): array => $record->getAttributes())->all()
        );

        return $model::hydrate($rows);
    }
}
