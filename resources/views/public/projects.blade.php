<x-layouts.public title="Proyectos" description="Proyectos y trabajos realizados en instalación, reparación y mantenimiento de bombas de agua.">
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Proyectos realizados</h1>
            <p class="mt-3 max-w-2xl text-lg text-slate-600">
                Una muestra de los trabajos que hemos llevado a cabo para nuestros clientes.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <div class="aspect-video bg-gradient-to-br from-sky-100 to-cyan-100 flex items-center justify-center text-sky-400">
                            @if ($project->image_url)
                                <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <x-service-icon name="wrench" class="h-10 w-10" />
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-semibold uppercase tracking-wider text-sky-600">{{ $project->location }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-slate-900">{{ $project->title }}</h2>
                            <p class="mt-2 text-sm text-slate-600">{{ $project->description }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.public>
