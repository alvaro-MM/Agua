<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Support\ImageUpload;
use App\Models\Product;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Producto')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Identificador (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('category')
                            ->label('Categoría')
                            ->required()
                            ->maxLength(255)
                            // Texto libre con sugerencias en vez de un desplegable
                            // cerrado: así se puede crear la primera categoría (y
                            // cualquier otra nueva) sin pasar por el código.
                            ->datalist(fn (): array => array_values(Product::categoryOptions()))
                            ->helperText('El catálogo público agrupa los productos por esta categoría.'),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Section::make('Imagen')
                    ->schema([
                        ImageUpload::make('image_path', 'catalogo')->hiddenLabel(),
                    ]),

                Section::make('Visibilidad')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Publicado')
                            ->helperText('Si lo desactivas, deja de aparecer en el catálogo.')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Destacar')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
