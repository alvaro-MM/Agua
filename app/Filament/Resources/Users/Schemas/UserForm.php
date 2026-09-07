<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Support\Permissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Select::make('roles')
                    ->label('Rol')
                    // El valor es el id del rol: es lo que espera el sync() de
                    // la relación. La etiqueta se traduce al vuelo.
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Role $record): string => Permissions::roleLabel($record->name))
                    ->required()
                    ->preload()
                    ->native(false)
                    // Al último administrador no se le puede quitar el rol: sin
                    // él nadie podría gestionar usuarios ni ajustes.
                    ->disabled(fn (?User $record): bool => $record?->isLastAdmin() ?? false)
                    ->helperText(fn (?User $record): ?string => $record?->isLastAdmin()
                        ? 'Es el único administrador. Nombra a otro antes de cambiarle el rol.'
                        : null),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    // El hash lo aplica el cast `hashed` del modelo User.
                    // Al editar, dejarla en blanco mantiene la actual.
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText(fn (string $operation): ?string => $operation === 'edit'
                        ? 'Déjala en blanco para no cambiarla.'
                        : null),
            ]);
    }
}
