<?php

namespace Tests\Feature\Panel;

use App\Models\User;
use App\Support\Permissions;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_un_invitado_es_redirigido_al_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_el_login_del_panel_es_accesible(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_un_usuario_sin_rol_no_entra_al_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_un_administrador_entra_al_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Permissions::ROLE_ADMIN);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_un_tecnico_entra_al_panel(): void
    {
        $tecnico = User::factory()->create();
        $tecnico->assignRole(Permissions::ROLE_TECNICO);

        $this->actingAs($tecnico)->get('/admin')->assertOk();
    }

    public function test_no_existe_registro_publico_en_el_panel(): void
    {
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_el_panel_ofrece_recuperacion_de_contrasena(): void
    {
        $this->get('/admin/password-reset/request')->assertOk();
    }
}
