@php
    $company = config('site.company');
    $contact = config('site.contact');
@endphp

<footer class="bg-slate-900 text-slate-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="flex items-center gap-2 font-bold text-lg text-white">
                    <svg class="h-6 w-6 text-sky-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2.5c3.5 4 6 7.4 6 10.5a6 6 0 1 1-12 0c0-3.1 2.5-6.5 6-10.5Z"/>
                    </svg>
                    {{ $company['name'] }}
                </div>
                <p class="mt-3 text-sm text-slate-400">{{ $company['tagline'] }}</p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Navegación</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Inicio</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white">Servicios</a></li>
                    <li><a href="{{ route('catalog') }}" class="hover:text-white">Catálogo</a></li>
                    <li><a href="{{ route('projects') }}" class="hover:text-white">Proyectos</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white">Sobre nosotros</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white">Contacto</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Contacto</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="tel:{{ $contact['phone_link'] }}" class="hover:text-white">{{ $contact['phone'] }}</a></li>
                    <li><a href="mailto:{{ $contact['email'] }}" class="hover:text-white">{{ $contact['email'] }}</a></li>
                    <li>{{ $contact['address'] }}, {{ $contact['postal_code'] }} {{ $company['city'] }}</li>
                    <li>{{ $contact['schedule'] }}</li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Legal</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('legal.notice') }}" class="hover:text-white">Aviso legal</a></li>
                    <li><a href="{{ route('legal.privacy') }}" class="hover:text-white">Política de privacidad</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 border-t border-slate-800 pt-6 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} {{ $company['name'] }}. Todos los derechos reservados.
        </div>
    </div>
</footer>
