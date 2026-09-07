<?php

namespace App\Filament\Resources\Services\Tables;

use App\Filament\Support\PublicationTable;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('icon')
                    ->label('Icono')
                    ->formatStateUsing(fn (string $state): string => Service::ICONS[$state] ?? $state)
                    ->badge()
                    ->color('gray'),

                PublicationTable::publishedColumn(),
                PublicationTable::featuredColumn(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id')
            ->filters([
                PublicationTable::publishedFilter(),
                PublicationTable::featuredFilter(),
                TrashedFilter::make()->label('Papelera'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    PublicationTable::publishBulkAction(Service::class),
                    PublicationTable::unpublishBulkAction(Service::class),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
