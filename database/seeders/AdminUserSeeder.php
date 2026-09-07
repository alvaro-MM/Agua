<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea el primer administrador. En producción se toman las credenciales de
     * ADMIN_EMAIL / ADMIN_PASSWORD; si no hay contraseña definida se genera una
     * aleatoria y se muestra por consola, para no dejar nunca una por defecto.
     */
    public function run(): void
    {
        $email = config('site.admin.email') ?: 'admin@'.parse_url(config('app.url'), PHP_URL_HOST);
        $password = config('site.admin.password');

        if ($existing = User::where('email', $email)->first()) {
            $existing->assignRole(Permissions::ROLE_ADMIN);
            $this->command?->info("El administrador {$email} ya existía; rol confirmado.");

            return;
        }

        $generated = null;

        if (blank($password)) {
            $password = $generated = Str::password(16);
        }

        $user = User::create([
            'name' => config('site.admin.name'),
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->email_verified_at = now();
        $user->save();

        $user->assignRole(Permissions::ROLE_ADMIN);

        $this->command?->info("Administrador creado: {$email}");

        if ($generated !== null) {
            $this->command?->warn("Contraseña generada: {$generated}");
            $this->command?->warn('Anótala ahora y cámbiala en el primer acceso.');
        }
    }
}
