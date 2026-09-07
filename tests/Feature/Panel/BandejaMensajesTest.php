<?php

namespace Tests\Feature\Panel;

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContactMessages\Pages\EditContactMessage;
use App\Models\ContactMessage;
use App\Models\User;
use App\Support\Permissions;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BandejaMensajesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function admin(): User
    {
        return tap(User::factory()->create())->assignRole(Permissions::ROLE_ADMIN);
    }

    private function tecnico(): User
    {
        return tap(User::factory()->create())->assignRole(Permissions::ROLE_TECNICO);
    }

    public function test_el_tecnico_y_el_administrador_ven_la_bandeja(): void
    {
        ContactMessage::factory()->create();

        $this->actingAs($this->tecnico())->get('/admin/contact-messages')->assertOk();
        $this->actingAs($this->admin())->get('/admin/contact-messages')->assertOk();
    }

    public function test_los_mensajes_no_se_dan_de_alta_a_mano(): void
    {
        $this->assertFalse(ContactMessageResource::canCreate());
        $this->assertFalse($this->admin()->can('create', ContactMessage::class));
    }

    public function test_abrir_un_mensaje_nuevo_lo_marca_como_leido(): void
    {
        $this->actingAs($this->tecnico());

        $mensaje = ContactMessage::factory()->create(['status' => ContactMessageStatus::Nuevo]);

        Livewire::test(EditContactMessage::class, ['record' => $mensaje->getRouteKey()]);

        $this->assertSame(ContactMessageStatus::Leido, $mensaje->refresh()->status);
    }

    public function test_marcar_como_atendido_registra_quien_y_cuando(): void
    {
        $tecnico = $this->tecnico();
        $this->actingAs($tecnico);

        $mensaje = ContactMessage::factory()->create(['status' => ContactMessageStatus::Leido]);

        Livewire::test(EditContactMessage::class, ['record' => $mensaje->getRouteKey()])
            ->fillForm([
                'status' => ContactMessageStatus::Atendido->value,
                'internal_notes' => 'Llamado el martes, presupuesto enviado.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $mensaje->refresh();

        $this->assertSame(ContactMessageStatus::Atendido, $mensaje->status);
        $this->assertSame($tecnico->id, $mensaje->handled_by);
        $this->assertNotNull($mensaje->handled_at);
        $this->assertSame('Llamado el martes, presupuesto enviado.', $mensaje->internal_notes);
    }

    public function test_reabrir_un_mensaje_borra_el_registro_de_atencion(): void
    {
        $this->actingAs($this->admin());

        $mensaje = ContactMessage::factory()->create(['status' => ContactMessageStatus::Nuevo]);
        $mensaje->update(['status' => ContactMessageStatus::Atendido]);

        $this->assertNotNull($mensaje->refresh()->handled_at);

        $mensaje->update(['status' => ContactMessageStatus::Nuevo]);

        $this->assertNull($mensaje->refresh()->handled_by);
        $this->assertNull($mensaje->handled_at);
    }

    public function test_el_tecnico_gestiona_pero_no_elimina(): void
    {
        $tecnico = $this->tecnico();
        $mensaje = ContactMessage::factory()->create();

        $this->assertTrue($tecnico->can('update', $mensaje));
        $this->assertFalse($tecnico->can('delete', $mensaje));
        $this->assertTrue($this->admin()->can('delete', $mensaje));
    }

    public function test_el_contador_del_menu_cuenta_los_nuevos(): void
    {
        ContactMessage::factory()->count(3)->create(['status' => ContactMessageStatus::Nuevo]);
        ContactMessage::factory()->create(['status' => ContactMessageStatus::Atendido]);

        $this->assertSame('3', ContactMessageResource::getNavigationBadge());
    }

    public function test_sin_mensajes_nuevos_no_hay_contador(): void
    {
        ContactMessage::factory()->create(['status' => ContactMessageStatus::Atendido]);

        $this->assertNull(ContactMessageResource::getNavigationBadge());
    }

    public function test_el_enlace_de_whatsapp_normaliza_el_telefono(): void
    {
        $conPrefijo = ContactMessage::factory()->create(['phone' => '+34 611 222 333']);
        $sinPrefijo = ContactMessage::factory()->create(['phone' => '611 222 333']);
        $sinTelefono = ContactMessage::factory()->create(['phone' => null]);

        $this->assertSame('https://wa.me/34611222333', $conPrefijo->whatsappUrl());
        $this->assertSame('https://wa.me/34611222333', $sinPrefijo->whatsappUrl());
        $this->assertNull($sinTelefono->whatsappUrl());
    }

    public function test_un_mensaje_del_formulario_publico_llega_a_la_bandeja(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Ana Pérez',
            'email' => 'ana@ejemplo.test',
            'phone' => '611222333',
            'message' => 'Se me ha averiado la bomba.',
            'privacy' => '1',
        ])->assertSessionHas('success');

        $this->actingAs($this->admin())
            ->get('/admin/contact-messages')
            ->assertOk()
            ->assertSee('Ana Pérez');

        $this->assertSame(1, ContactMessage::query()->nuevos()->count());
    }
}
