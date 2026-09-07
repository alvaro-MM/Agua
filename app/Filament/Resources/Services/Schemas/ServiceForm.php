<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Servicio')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                                // El slug es la ancla de la página de servicios
                                // (/servicios#instalacion): sólo se autogenera
                                // al crear, para no romper enlaces existentes.
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Identificador (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Se usa como ancla en /servicios. Cambiarlo rompe los enlaces que ya apunten aquí.'),

                        Textarea::make('excerpt')
                            ->label('Resumen')
                            ->required()
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('La frase corta que aparece en la portada y en el listado.'),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(5),

                        Select::make('icon')
                            ->label('Icono')
                            ->options(Service::ICONS)
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Section::make('Qué incluye')
                    ->schema([
                        Repeater::make('features')
                            ->label('Puntos del servicio')
                            ->hiddenLabel()
                            ->simple(
                                TextInput::make('feature')
                                    ->label('Punto')
                                    ->required()
                                    ->maxLength(255)
                            )
                            ->addActionLabel('Añadir punto')
                            ->reorderable()
                            ->default([]),
                    ]),

                Section::make('Visibilidad')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Publicado')
                            ->helperText('Si lo desactivas, deja de aparecer en la web.')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Destacar en la portada')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
