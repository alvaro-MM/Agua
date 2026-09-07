<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (ContactMessage $record): string => $record->created_at->diffForHumans()),

                TextColumn::make('name')
                    ->label('Remitente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Correo copiado'),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable()
                    ->placeholder('No indicado'),

                TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(60)
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('handler.name')
                    ->label('Atendido por')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Lo más reciente primero: es como se trabaja una bandeja.
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(ContactMessageStatus::class)
                    ->multiple(),

                Filter::make('pendientes')
                    ->label('Solo pendientes')
                    ->query(fn (Builder $query): Builder => $query->pendientes()),

                TrashedFilter::make()->label('Papelera'),
            ])
            ->recordActions([
                EditAction::make()->label('Abrir'),

                // El resto en el menú de la fila, para no saturar el listado.
                ActionGroup::make([
                    Action::make('responder')
                        ->label('Responder por correo')
                        ->icon('heroicon-o-envelope')
                        ->url(fn (ContactMessage $record): string => 'mailto:'.$record->email)
                        ->openUrlInNewTab(),

                    Action::make('whatsapp')
                        ->label('WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->visible(fn (ContactMessage $record): bool => $record->whatsappUrl() !== null)
                        ->url(fn (ContactMessage $record): ?string => $record->whatsappUrl())
                        ->openUrlInNewTab(),

                    DeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::marcarComo(ContactMessageStatus::Leido, 'heroicon-o-envelope-open'),
                    self::marcarComo(ContactMessageStatus::Atendido, 'heroicon-o-check-circle'),
                    self::marcarComo(ContactMessageStatus::Descartado, 'heroicon-o-x-circle'),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * El cambio se aplica registro a registro (y no con un update masivo) para
     * que el evento del modelo anote quién y cuándo lo atendió.
     */
    private static function marcarComo(ContactMessageStatus $status, string $icon): BulkAction
    {
        return BulkAction::make('marcar_'.$status->value)
            ->label('Marcar como '.mb_strtolower($status->getLabel()))
            ->icon($icon)
            ->color($status->getColor())
            ->requiresConfirmation()
            ->action(fn (Collection $records) => $records->each->update(['status' => $status]))
            ->deselectRecordsAfterCompletion();
    }
}
