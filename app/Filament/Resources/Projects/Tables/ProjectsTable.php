<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Filament\Support\PublicationTable;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Foto')
                    ->square(),

                TextColumn::make('title')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('location')
                    ->label('Localidad')
                    ->searchable()
                    ->sortable(),

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
                    PublicationTable::publishBulkAction(Project::class),
                    PublicationTable::unpublishBulkAction(Project::class),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
