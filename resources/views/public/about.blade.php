@php
    $company = config('site.company');
    $contact = config('site.contact');
@endphp

<x-layouts.public title="Sobre nosotros" description="Conoce a {{ $company['name'] }}: profesionales de las bombas de agua con años de experiencia.">
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Sobre nosotros</h1>
            <p class="mt-3 max-w-2xl text-lg text-slate-600">{{ $company['tagline'] }}</p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 prose prose-slate">
            <p class="text-lg text-slate-700 leading-relaxed">
                En <strong>{{ $company['name'] }}</strong> llevamos desde {{ $company['founded_year'] }} ofreciendo
                soluciones profesionales en bombas de agua. Somos un equipo cercano y especializado, comprometido con
                dar un servicio rápido, honesto y de calidad.
            </p>
            <p class="mt-6 text-slate-700 leading-relaxed">
                Trabajamos para hogares, comunidades de vecinos, explotaciones agrícolas e industria en
                {{ implode(', ', $company['service_areas']) }}. Nuestro objetivo es que nunca te falte el suministro
                de agua, con instalaciones fiables y un mantenimiento que evite sorpresas.
            </p>

            <div class="mt-12 grid gap-6 sm:grid-cols-3 not-prose">
                <div class="rounded-xl border border-slate-200 p-6 text-center">
                    <p class="text-2xl font-bold text-sky-700">Cercanía</p>
                    <p class="mt-2 text-sm text-slate-600">Trato directo y honesto</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-6 text-center">
                    <p class="text-2xl font-bold text-sky-700">Rapidez</p>
                    <p class="mt-2 text-sm text-slate-600">Respuesta ágil ante averías</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-6 text-center">
                    <p class="text-2xl font-bold text-sky-700">Calidad</p>
                    <p class="mt-2 text-sm text-slate-600">Materiales y trabajo garantizados</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-sky-700">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 text-center">
            <h2 class="text-3xl font-bold text-white">Hablemos de tu proyecto</h2>
            <p class="mt-3 text-sky-100">Estamos disponibles {{ $contact['schedule'] }}.</p>
            <a href="{{ route('contact') }}" class="mt-8 inline-flex items-center rounded-md bg-white px-6 py-3 font-semibold text-sky-700 hover:bg-sky-50">
                Contactar
            </a>
        </div>
    </section>
</x-layouts.public>
