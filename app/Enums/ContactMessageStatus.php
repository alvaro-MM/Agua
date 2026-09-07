<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactMessageStatus: string implements HasColor, HasLabel
{
    case Nuevo = 'nuevo';
    case Leido = 'leido';
    case Atendido = 'atendido';
    case Descartado = 'descartado';

    public function getLabel(): string
    {
        return match ($this) {
            self::Nuevo => 'Nuevo',
            self::Leido => 'Leído',
            self::Atendido => 'Atendido',
            self::Descartado => 'Descartado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Nuevo => 'warning',
            self::Leido => 'info',
            self::Atendido => 'success',
            self::Descartado => 'gray',
        };
    }

    /** Un mensaje deja de estar pendiente cuando se atiende o se descarta. */
    public function isPending(): bool
    {
        return in_array($this, [self::Nuevo, self::Leido], strict: true);
    }
}
