@php
    $company = config('site.company');
    $contact = config('site.contact');
    $services = config('site.services');
    $projects = config('site.projects');
@endphp

<x-layouts.public>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-sky-700 via-sky-600 to-cyan-600 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="max-w-2xl">
                <p class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-sm font-medium">
                    {{ $company['tagline'] }}
                </p>
                <h1 class="mt-5 text-4xl sm:text-5xl font-bold tracking-tight">
                    Expertos en bombas de agua para tu hogar, comunidad e industria
                </h1>
                <p class="mt-5 text-lg text-sky-50">
                    {{ $company['description'] }}
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center rounded-md bg-white px-6 py-3 text-base font-semibold text-sky-700 shadow-sm transition hover:bg-sky-50">
                        Pedir presupuesto
                    </a>
                    <a href="tel:{{ $contact['phone_link'] }}"
                       class="inline-flex items-center rounded-md border border-white/40 px-6 py-3 text-base font-semibold text-white transition hover:bg-white/10">
                        Llamar {{ $contact['phone'] }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Ventajas --}}
    <section class="border-b border-slate-100 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid gap-6 sm:grid-cols-3 text-center">
                <div>
                    <p class="text-3xl font-bold text-sky-700">+{{ date('Y') - $company['founded_year'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">años de experiencia</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-sky-700">24/7</p>
                    <p class="mt-1 text-sm text-slate-600">servicio de urgencias</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-sky-700">100%</p>
                    <p class="mt-1 text-sm text-slate-600">trabajo garantizado</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Servicios --}}
    <section class="py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Nuestros servicios</h2>
                <p class="mt-3 text-slate-600">Soluciones completas para todo el ciclo de vida de tu bomba de agua.</p>
            </div>
            <div class="mt-12 grid gap-8 md:grid-cols-3">
                @foreach ($services as $service)
                    <div class="rounded-2xl border border-slate-200 p-8 transition hover:shadow-lg hover:border-sky-200">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                            <x-service-icon :name="$service['icon']" class="h-6 w-6" />
                        </div>
                        <h3 class="mt-5 text-xl font-semibold text-slate-900">{{ $service['title'] }}</h3>
                        <p class="mt-3 text-slate-600">{{ $service['excerpt'] }}</p>
                        <a href="{{ route('services') }}#{{ $service['slug'] }}"
                           class="mt-4 inline-flex items-center text-sm font-semibold text-sky-700 hover:text-sky-800">
                            Saber más &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Proyectos destacados --}}
    <section class="bg-slate-50 py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900">Proyectos recientes</h2>
                    <p class="mt-3 text-slate-600">Algunos de los trabajos que hemos realizado.</p>
                </div>
                <a href="{{ route('projects') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-800">Ver todos &rarr;</a>
            </div>
            <div class="mt-10 grid gap-8 md:grid-cols-3">
                @foreach ($projects as $project)
                    <article class="overflow-hidden rounded-2xl bg-white border border-slate-200">
                        <div class="aspect-video bg-gradient-to-br from-sky-100 to-cyan-100 flex items-center justify-center text-sky-400">
                            @if ($project['image'])
                                <img src="{{ asset($project['image']) }}" alt="{{ $project['title'] }}" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <x-service-icon name="wrench" class="h-10 w-10" />
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sky-600">{{ $project['location'] }}</p>
                            <h3 class="mt-2 font-semibold text-slate-900">{{ $project['title'] }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $project['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-sky-700">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 text-center">
            <h2 class="text-3xl font-bold text-white">¿Necesitas ayuda con tu bomba de agua?</h2>
            <p class="mt-3 text-sky-100">Cuéntanos qué necesitas y te daremos un presupuesto sin compromiso.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('contact') }}" class="inline-flex items-center rounded-md bg-white px-6 py-3 font-semibold text-sky-700 hover:bg-sky-50">
                    Contactar ahora
                </a>
                <a href="tel:{{ $contact['phone_link'] }}" class="inline-flex items-center rounded-md border border-white/40 px-6 py-3 font-semibold text-white hover:bg-white/10">
                    {{ $contact['phone'] }}
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>
