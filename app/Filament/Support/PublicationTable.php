<?php

namespace App\Filament\Support;

use Filament\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Piezas de tabla compartidas por los tres recursos de contenido (servicios,
 * catálogo y proyectos), que comparten el mismo estado de publicación.
 */
final class PublicationTable
{
    public static function publishedColumn(): IconColumn
    {
        return IconColumn::make('is_published')
            ->label('Publicado')
            ->boolean()
            ->sortable();
    }

    public static function featuredColumn(): IconColumn
    {
        return IconColumn::make('is_featured')
            ->label('En portada')
            ->boolean()
            ->sortable();
    }

    public static function publishedFilter(): TernaryFilter
    {
        return TernaryFilter::make('is_published')
            ->label('Publicado')
            ->placeholder('Todos')
            ->trueLabel('Solo publicados')
            ->falseLabel('Solo borradores');
    }

    public static function featuredFilter(): TernaryFilter
    {
        return TernaryFilter::make('is_featured')
            ->label('En portada')
            ->placeholder('Todos')
            ->trueLabel('Solo destacados')
            ->falseLabel('Sin destacar');
    }

    /** @param  class-string<Model>  $model */
    public static function publishBulkAction(string $model): BulkAction
    {
        return BulkAction::make('publicar')
            ->label('Publicar')
            ->icon('heroicon-o-eye')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (): bool => self::canPublish($model))
            ->action(fn (Collection $records) => $records->each->update(['is_published' => true]))
            ->deselectRecordsAfterCompletion();
    }

    /** @param  class-string<Model>  $model */
    public static function unpublishBulkAction(string $model): BulkAction
    {
        return BulkAction::make('despublicar')
            ->label('Pasar a borrador')
            ->icon('heroicon-o-eye-slash')
            ->color('warning')
            ->requiresConfirmation()
            ->visible(fn (): bool => self::canPublish($model))
            ->action(fn (Collection $records) => $records->each->update(['is_published' => false]))
            ->deselectRecordsAfterCompletion();
    }

    /**
     * Publicar y destacar son decisiones editoriales: las reserva la Policy al
     * permiso `publicar` del recurso.
     *
     * @param  class-string<Model>  $model
     */
    public static function canPublish(string $model): bool
    {
        return auth()->user()?->can('publish', $model) ?? false;
    }
}
