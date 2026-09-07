<?php

namespace Tests\Unit;

use App\Filament\Support\InitialsAvatarProvider;
use App\Models\User;
use Tests\TestCase;

class InitialsAvatarProviderTest extends TestCase
{
    private function avatarDe(string $name): string
    {
        $svg = (new InitialsAvatarProvider)->get(new User(['name' => $name]));

        return base64_decode(str_replace('data:image/svg+xml;base64,', '', $svg));
    }

    public function test_usa_las_dos_primeras_iniciales(): void
    {
        $this->assertStringContainsString('>MG<', $this->avatarDe('Miguel Gómez Pérez'));
    }

    public function test_funciona_con_un_solo_nombre(): void
    {
        $this->assertStringContainsString('>M<', $this->avatarDe('Miguel'));
    }

    public function test_se_salta_la_puntuacion_inicial(): void
    {
        $this->assertStringContainsString('>SA<', $this->avatarDe('[SYSTEM] Admin'));
    }

    public function test_no_genera_svg_sin_iniciales(): void
    {
        $this->assertStringContainsString('>?<', $this->avatarDe('   '));
    }

    public function test_escapa_el_contenido_del_nombre(): void
    {
        $svg = $this->avatarDe('<script>alert(1)</script> Pérez');

        $this->assertStringNotContainsString('<script>', $svg);
    }

    public function test_no_llama_a_ningun_servicio_externo(): void
    {
        $this->assertStringStartsWith(
            'data:image/svg+xml;base64,',
            (new InitialsAvatarProvider)->get(new User(['name' => 'Miguel']))
        );
    }
}
