<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Lo que escribió el remitente no se edita: es el documento
                // recibido. Sólo se puede anotar y cambiarle el estado.
                Section::make('Mensaje recibido')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->disabled(),

                        TextInput::make('email')
                            ->label('Correo')
                            ->disabled(),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->disabled()
                            ->placeholder('No indicado'),

                        TextInput::make('created_at')
                            ->label('Recibido')
                            ->disabled()
                            ->dehydrated(false)
                            // Desde el record y no desde $state: Filament ya
                            // entrega el estado como cadena, no como Carbon.
                            ->formatStateUsing(fn (?ContactMessage $record): ?string => $record?->created_at?->format('d/m/Y H:i')),

                        Textarea::make('message')
                            ->label('Mensaje')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Gestión')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options(ContactMessageStatus::class)
                            ->required()
                            ->native(false),

                        TextInput::make('handled_by')
                            ->label('Atendido por')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?ContactMessage $record): string => $record?->handler?->name ?? '—')
                            ->hint(fn (?ContactMessage $record): ?string => $record?->handled_at?->format('d/m/Y H:i')),

                        Textarea::make('internal_notes')
                            ->label('Notas internas')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Solo se ven aquí. El remitente nunca las recibe.'),
                    ])
                    ->columns(2),
            ]);
    }
}
