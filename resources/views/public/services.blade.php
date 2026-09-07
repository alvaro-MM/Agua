<x-layouts.public title="Servicios" description="Instalación, reparación y mantenimiento de bombas de agua. Descubre todos nuestros servicios profesionales.">
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Servicios</h1>
            <p class="mt-3 max-w-2xl text-lg text-slate-600">
                Ofrecemos un servicio integral para bombas de agua: desde la instalación inicial hasta el mantenimiento y la reparación de averías.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
            @foreach ($services as $service)
                <div id="{{ $service->slug }}" class="grid gap-8 lg:grid-cols-3 scroll-mt-24">
                    <div class="lg:col-span-1">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                            <x-service-icon :name="$service->icon" class="h-6 w-6" />
                        </div>
                        <h2 class="mt-4 text-2xl font-bold text-slate-900">{{ $service->title }}</h2>
                        <p class="mt-3 text-slate-600">{{ $service->excerpt }}</p>
                    </div>
                    <div class="lg:col-span-2">
                        <p class="text-slate-700 leading-relaxed">{{ $service->description }}</p>
                        <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                            @foreach ($service->features as $feature)
                                <li class="flex items-start gap-3">
                                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-sky-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-slate-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-sky-700">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 text-center">
            <h2 class="text-3xl font-bold text-white">¿No encuentras lo que buscas?</h2>
            <p class="mt-3 text-sky-100">Escríbenos y estudiaremos tu caso concreto.</p>
            <a href="{{ route('contact') }}" class="mt-8 inline-flex items-center rounded-md bg-white px-6 py-3 font-semibold text-sky-700 hover:bg-sky-50">
                Pedir presupuesto
            </a>
        </div>
    </section>
</x-layouts.public>
