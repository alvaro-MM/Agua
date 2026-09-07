<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    /** @return array<string, Tab> */
    public function getTabs(): array
    {
        return [
            'pendientes' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->pendientes())
                ->badge(ContactMessage::query()->pendientes()->count()),

            'todos' => Tab::make('Todos'),

            'atendidos' => Tab::make('Atendidos')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContactMessageStatus::Atendido)),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'pendientes';
    }
}
