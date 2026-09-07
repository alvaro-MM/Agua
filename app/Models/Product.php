<?php

namespace App\Models;

use App\Concerns\FlushesPublicCache;
use App\Concerns\HasImagePath;
use App\Concerns\HasPublicationState;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['slug', 'name', 'category', 'description', 'image_path', 'is_published', 'is_featured'])]
class Product extends Model
{
    use FlushesPublicCache, HasFactory, HasImagePath, HasPublicationState, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Categorías ya en uso, para ofrecerlas en el formulario sin obligar a una
     * tabla aparte. La vista del catálogo agrupa por este campo.
     *
     * @return array<string, string>
     */
    public static function categoryOptions(): array
    {
        return static::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category', 'category')
            ->all();
    }
}
