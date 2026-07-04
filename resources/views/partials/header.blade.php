@php
    $company = config('site.company');
    $contact = config('site.contact');
    $nav = [
        ['label' => 'Inicio', 'route' => 'home'],
        ['label' => 'Servicios', 'route' => 'services'],
        ['label' => 'Catálogo', 'route' => 'catalog'],
        ['label' => 'Proyectos', 'route' => 'projects'],
        ['label' => 'Sobre nosotros', 'route' => 'about'],
        ['label' => 'Contacto', 'route' => 'contact'],
    ];
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg text-sky-700">
                <svg class="h-7 w-7 text-sky-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2.5c3.5 4 6 7.4 6 10.5a6 6 0 1 1-12 0c0-3.1 2.5-6.5 6-10.5Z"/>
                </svg>
                <span>{{ $company['name'] }}</span>
            </a>

            <nav class="hidden lg:flex items-center gap-1">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs($item['route']) ? 'text-sky-700 bg-sky-50' : 'text-slate-600 hover:text-sky-700 hover:bg-slate-50' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="hidden lg:flex items-center gap-3">
                <a href="tel:{{ $contact['phone_link'] }}" class="text-sm font-semibold text-slate-700 hover:text-sky-700">
                    {{ $contact['phone'] }}
                </a>
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                    Pedir presupuesto
                </a>
            </div>

            <button type="button" @click="open = !open"
                    class="lg:hidden inline-flex items-center justify-center rounded-md p-2 text-slate-600 hover:bg-slate-100"
                    aria-label="Abrir menú">
                <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak class="lg:hidden border-t border-slate-200 bg-white">
        <nav class="space-y-1 px-4 py-3">
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="block rounded-md px-3 py-2 text-base font-medium
                          {{ request()->routeIs($item['route']) ? 'text-sky-700 bg-sky-50' : 'text-slate-700 hover:bg-slate-50' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('contact') }}"
               class="mt-2 block rounded-md bg-sky-600 px-3 py-2 text-center text-base font-semibold text-white">
                Pedir presupuesto
            </a>
        </nav>
    </div>
</header>
