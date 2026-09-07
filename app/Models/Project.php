<?php

namespace App\Models;

use App\Concerns\FlushesPublicCache;
use App\Concerns\HasImagePath;
use App\Concerns\HasPublicationState;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['slug', 'title', 'location', 'description', 'image_path', 'is_published', 'is_featured'])]
class Project extends Model
{
    use FlushesPublicCache, HasFactory, HasImagePath, HasPublicationState, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }
}
