<?php

namespace App\Filament\Pages;

use App\Models\SiteSettings;
use App\Support\Permissions;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Datos de la empresa, contacto y redes sociales.
 *
 * No es un CRUD: es una única fila que alimenta la cabecera, el pie, el botón
 * de WhatsApp, la página de contacto, el aviso legal y el Schema.org de la web
 * pública. Antes había que tocar config/site.php y desplegar.
 */
class SiteSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'ajustes';

    protected static ?string $title = 'Ajustes del sitio';

    protected static ?string $navigationLabel = 'Ajustes del sitio';

    protected string $view = 'filament.pages.site-settings';

    /** @var array<string, mixed> */
    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can(Permissions::AJUSTES) ?? false;
    }

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->form->fill(SiteSettings::current()->attributesToArray());
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Empresa')
                    ->description('Se usa en la cabecera, el pie, el aviso legal y los datos que lee Google.')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Nombre comercial')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('legal_name')
                            ->label('Razón social')
                            ->maxLength(255)
                            ->helperText('Aparece en el aviso legal.'),

                        TextInput::make('nif')
                            ->label('NIF / CIF')
                            ->maxLength(20),

                        TextInput::make('founded_year')
                            ->label('Año de fundación')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue((int) date('Y'))
                            ->helperText('La portada calcula con él los años de experiencia.'),

                        TextInput::make('tagline')
                            ->label('Lema')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripción de la empresa')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Se usa en la portada y como descripción para buscadores.'),

                        TextInput::make('city')
                            ->label('Ciudad'),

                        TagsInput::make('service_areas')
                            ->label('Zonas de actuación')
                            ->placeholder('Añadir zona')
                            ->helperText('Pulsa Intro tras cada zona.'),
                    ])
                    ->columns(2),

                Section::make('Contacto')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Teléfono (como se muestra)')
                            ->tel()
                            ->maxLength(30),

                        TextInput::make('phone_link')
                            ->label('Teléfono (para el enlace)')
                            ->tel()
                            ->maxLength(30)
                            ->helperText('Sin espacios, con prefijo. Ej.: +34600000000'),

                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->maxLength(30)
                            ->helperText('Solo dígitos con prefijo, sin +. Ej.: 34600000000'),

                        TextInput::make('email')
                            ->label('Correo público')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('notify_email')
                            ->label('Correo de avisos')
                            ->email()
                            ->maxLength(255)
                            ->helperText('A esta dirección llegan los mensajes del formulario de contacto.'),

                        TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255),

                        TextInput::make('postal_code')
                            ->label('Código postal')
                            ->maxLength(10),

                        TextInput::make('schedule')
                            ->label('Horario')
                            ->maxLength(255),

                        TextInput::make('schedule_short')
                            ->label('Horario abreviado')
                            ->maxLength(255)
                            ->helperText('Versión corta para la cabecera. Ej.: L-V 8:00-18:00'),

                        Textarea::make('whatsapp_message')
                            ->label('Mensaje predefinido de WhatsApp')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('maps_embed')
                            ->label('Mapa incrustado')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('URL del iframe de Google Maps. Déjalo vacío para no mostrar mapa.'),
                    ])
                    ->columns(2),

                Section::make('Redes sociales')
                    ->schema([
                        TextInput::make('facebook')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),

                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    /** @return array<Action> */
    protected function getFormActions(): array
    {
        return [
            Action::make('guardar')
                ->label('Guardar cambios')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $this->authorizeAccess();

        // El guardado invalida la caché pública (FlushesPublicCache), así que
        // el cambio se ve en la web al momento.
        SiteSettings::current()->update($this->form->getState());

        Notification::make()
            ->title('Ajustes guardados')
            ->body('Los cambios ya se ven en la web pública.')
            ->success()
            ->send();
    }
}
