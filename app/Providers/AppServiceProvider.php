<?php

namespace App\Providers;

use App\Support\PublicContent;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->shareSiteSettings();
    }

    /**
     * Los datos de empresa y contacto aparecen en la cabecera, el pie, el botón
     * de WhatsApp, el aviso legal y el Schema.org. En vez de repetir la consulta
     * en cada vista, se comparten como $settings allí donde hacen falta.
     *
     * La lectura está cacheada (PublicContent) y el panel invalida esa caché al
     * guardar, así que un cambio de teléfono se ve al instante en toda la web.
     */
    private function shareSiteSettings(): void
    {
        View::composer(
            ['components.layouts.public', 'partials.*', 'public.*', 'emails.*'],
            fn ($view) => $view->with('settings', PublicContent::settings())
        );
    }
}
