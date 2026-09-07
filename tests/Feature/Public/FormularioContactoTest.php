<?php

namespace Tests\Feature\Public;

use App\Enums\ContactMessageStatus;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactMessage;
use App\Models\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FormularioContactoTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function datosValidos(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ana Pérez',
            'email' => 'ana@ejemplo.test',
            'phone' => '600111222',
            'message' => 'Necesito reparar la bomba del pozo.',
            'privacy' => '1',
        ], $overrides);
    }

    public function test_guarda_el_mensaje_y_avisa_al_correo_de_los_ajustes(): void
    {
        Mail::fake();

        SiteSettings::current()->update(['notify_email' => 'avisos@electrobombas.test']);

        $this->post(route('contact.store'), $this->datosValidos())
            ->assertRedirect(route('contact'))
            ->assertSessionHas('success');

        $mensaje = ContactMessage::sole();

        $this->assertSame('Ana Pérez', $mensaje->name);
        $this->assertSame(ContactMessageStatus::Nuevo, $mensaje->status);

        Mail::assertSent(
            ContactFormSubmitted::class,
            fn (ContactFormSubmitted $mail): bool => $mail->hasTo('avisos@electrobombas.test')
        );
    }

    public function test_cambiar_el_correo_de_avisos_redirige_los_mensajes(): void
    {
        Mail::fake();

        SiteSettings::current()->update(['notify_email' => 'viejo@electrobombas.test']);
        SiteSettings::current()->update(['notify_email' => 'nuevo@electrobombas.test']);

        $this->post(route('contact.store'), $this->datosValidos());

        Mail::assertSent(
            ContactFormSubmitted::class,
            fn (ContactFormSubmitted $mail): bool => $mail->hasTo('nuevo@electrobombas.test')
        );
    }

    public function test_sin_correo_de_avisos_el_mensaje_se_guarda_igualmente(): void
    {
        Mail::fake();

        SiteSettings::current()->update(['notify_email' => null]);

        $this->post(route('contact.store'), $this->datosValidos())
            ->assertSessionHas('success');

        $this->assertSame(1, ContactMessage::count());
        Mail::assertNothingSent();
    }

    public function test_el_honeypot_bloquea_el_spam(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), $this->datosValidos(['website' => 'http://spam.test']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_exige_aceptar_la_politica_de_privacidad(): void
    {
        $this->post(route('contact.store'), $this->datosValidos(['privacy' => null]))
            ->assertSessionHasErrors('privacy');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_valida_los_campos_obligatorios(): void
    {
        $this->post(route('contact.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'message', 'privacy']);
    }
}
