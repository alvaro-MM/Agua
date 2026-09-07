<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Support\PublicationTable;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Imagen')
                    ->square(),

                TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray')
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
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(fn (): array => Product::categoryOptions()),
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
                    PublicationTable::publishBulkAction(Product::class),
                    PublicationTable::unpublishBulkAction(Product::class),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
