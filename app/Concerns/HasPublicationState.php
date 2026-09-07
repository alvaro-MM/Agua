<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Estado de publicación común a servicios, productos y proyectos: Miguel puede
 * preparar contenido en borrador y decidir qué destaca en la portada.
 */
trait HasPublicationState
{
    /** @param  Builder<static>  $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /** @param  Builder<static>  $query */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }
}
