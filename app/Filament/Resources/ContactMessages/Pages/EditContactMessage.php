<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function getTitle(): string
    {
        return 'Mensaje de '.$this->getRecord()->name;
    }

    /**
     * Abrir un mensaje nuevo lo pasa a leído: así el contador del menú refleja
     * lo que queda de verdad por mirar.
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $mensaje = $this->getRecord();

        if ($mensaje->status === ContactMessageStatus::Nuevo && auth()->user()?->can('update', $mensaje)) {
            $mensaje->update(['status' => ContactMessageStatus::Leido]);
            $this->fillForm();
        }
    }

    protected function getHeaderActions(): array
    {
        /** @var ContactMessage $mensaje */
        $mensaje = $this->getRecord();

        return [
            Action::make('responder')
                ->label('Responder por correo')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->url('mailto:'.$mensaje->email)
                ->openUrlInNewTab(),

            Action::make('llamar')
                ->label('Llamar')
                ->icon('heroicon-o-phone')
                ->color('gray')
                ->visible(filled($mensaje->phone))
                ->url('tel:'.$mensaje->phone),

            Action::make('whatsapp')
                ->label('WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('gray')
                ->visible($mensaje->whatsappUrl() !== null)
                ->url((string) $mensaje->whatsappUrl())
                ->openUrlInNewTab(),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
