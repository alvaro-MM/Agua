<?php

namespace App\Models;

use App\Concerns\FlushesPublicCache;
use App\Concerns\HasPublicationState;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['slug', 'title', 'excerpt', 'description', 'icon', 'features', 'is_published', 'is_featured'])]
class Service extends Model
{
    use FlushesPublicCache, HasFactory, HasPublicationState, SoftDeletes;

    /**
     * Iconos disponibles. Las claves son las que entiende el componente
     * <x-service-icon>: añadir una aquí exige añadirla también allí.
     *
     * @var array<string, string>
     */
    public const ICONS = [
        'wrench' => 'Llave inglesa',
        'bolt' => 'Rayo',
        'shield' => 'Escudo',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }
}
