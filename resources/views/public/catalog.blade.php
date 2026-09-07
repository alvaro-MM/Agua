<x-layouts.public title="Catálogo" description="Catálogo de bombas de agua, grupos de presión, depósitos, recambios y accesorios.">
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Catálogo</h1>
            <p class="mt-3 max-w-2xl text-lg text-slate-600">
                Trabajamos con las principales marcas de bombas y accesorios. Consúltanos por modelos y disponibilidad.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-14">
            @foreach ($catalog as $category => $items)
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $category }}</h2>
                    <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($items as $item)
                            <article class="overflow-hidden rounded-2xl border border-slate-200 transition hover:shadow-lg">
                                <div class="aspect-video bg-gradient-to-br from-sky-100 to-cyan-100 flex items-center justify-center text-sky-400">
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <x-service-icon name="wrench" class="h-10 w-10" />
                                    @endif
                                </div>
                                <div class="p-6">
                                    <h3 class="font-semibold text-slate-900">{{ $item->name }}</h3>
                                    <p class="mt-2 text-sm text-slate-600">{{ $item->description }}</p>
                                    <a href="{{ route('contact') }}" class="mt-4 inline-flex items-center text-sm font-semibold text-sky-700 hover:text-sky-800">
                                        Solicitar información &rarr;
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.public>
