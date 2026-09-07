<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\ImageUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Proyecto')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
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

                        TextInput::make('location')
                            ->label('Localidad')
                            ->maxLength(255)
                            ->helperText('Aparece sobre el título en el portfolio. Ayuda al SEO local.'),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Section::make('Fotografía')
                    ->schema([
                        ImageUpload::make('image_path', 'proyectos')->hiddenLabel(),
                    ]),

                Section::make('Visibilidad')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Publicado')
                            ->helperText('Si lo desactivas, deja de aparecer en el portfolio.')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Destacar en la portada')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
