<?php

namespace Tests\Feature\Panel;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use App\Support\Permissions;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
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

    public function test_un_tecnico_no_puede_listar_usuarios(): void
    {
        $this->actingAs($this->tecnico())
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_un_administrador_puede_listar_usuarios(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/users')
            ->assertOk();
    }

    public function test_crear_un_usuario_le_asigna_el_rol_elegido(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Ana Técnica',
                'email' => 'ana@electrobombas.test',
                'roles' => Role::findByName(Permissions::ROLE_TECNICO)->getKey(),
                'password' => 'contrasena-larga',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $creado = User::where('email', 'ana@electrobombas.test')->sole();

        $this->assertTrue($creado->hasRole(Permissions::ROLE_TECNICO));
        $this->assertTrue(Hash::check('contrasena-larga', $creado->password));
    }

    public function test_editar_sin_contrasena_conserva_la_actual(): void
    {
        $this->actingAs($this->admin());

        $otro = $this->tecnico();
        $hashOriginal = $otro->password;

        Livewire::test(EditUser::class, ['record' => $otro->getRouteKey()])
            ->fillForm([
                'name' => 'Nombre cambiado',
                'password' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Nombre cambiado', $otro->refresh()->name);
        $this->assertSame($hashOriginal, $otro->password);
    }

    public function test_no_se_puede_eliminar_al_ultimo_administrador(): void
    {
        $admin = $this->admin();
        $otroAdmin = $this->admin();

        // Con dos administradores, uno puede borrar al otro.
        $this->assertTrue($admin->can('delete', $otroAdmin));

        $otroAdmin->delete();

        // Al quedar solo, ya no.
        $this->assertTrue($admin->refresh()->isLastAdmin());
        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_nadie_puede_eliminarse_a_si_mismo(): void
    {
        $admin = $this->admin();
        $this->admin(); // un segundo admin, para descartar la regla del último

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_un_tecnico_no_puede_eliminar_usuarios(): void
    {
        $tecnico = $this->tecnico();
        $admin = $this->admin();

        $this->assertFalse($tecnico->can('delete', $admin));
        $this->assertFalse($tecnico->can('create', User::class));
    }
}
