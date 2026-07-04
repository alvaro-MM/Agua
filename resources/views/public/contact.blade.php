@php
    $company = config('site.company');
    $contact = config('site.contact');
@endphp

<x-layouts.public title="Contacto" description="Contacta con {{ $company['name'] }}. Presupuesto sin compromiso para instalación, reparación y mantenimiento de bombas de agua.">
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Contacto</h1>
            <p class="mt-3 max-w-2xl text-lg text-slate-600">
                Cuéntanos qué necesitas y te responderemos lo antes posible con un presupuesto sin compromiso.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid gap-12 lg:grid-cols-2">
            {{-- Datos de contacto --}}
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Datos de contacto</h2>
                <dl class="mt-6 space-y-5">
                    <div class="flex items-start gap-4">
                        <span class="mt-1 text-sky-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        </span>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Teléfono</dt>
                            <dd><a href="tel:{{ $contact['phone_link'] }}" class="text-slate-900 hover:text-sky-700">{{ $contact['phone'] }}</a></dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="mt-1 text-sky-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        </span>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Email</dt>
                            <dd><a href="mailto:{{ $contact['email'] }}" class="text-slate-900 hover:text-sky-700">{{ $contact['email'] }}</a></dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="mt-1 text-sky-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        </span>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Dirección</dt>
                            <dd class="text-slate-900">{{ $contact['address'] }}, {{ $contact['postal_code'] }} {{ $company['city'] }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="mt-1 text-sky-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </span>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Horario</dt>
                            <dd class="text-slate-900">{{ $contact['schedule'] }}</dd>
                        </div>
                    </div>
                </dl>

                @if ($contact['maps_embed'])
                    <div class="mt-8 overflow-hidden rounded-xl border border-slate-200">
                        {!! $contact['maps_embed'] !!}
                    </div>
                @endif
            </div>

            {{-- Formulario --}}
            <div>
                <div class="rounded-2xl border border-slate-200 p-6 sm:p-8">
                    @if (session('success'))
                        <div class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- Honeypot anti-spam --}}
                        <div class="hidden" aria-hidden="true">
                            <label for="website">No rellenar</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Nombre *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500 @error('name') border-red-400 @enderror">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700">Email *</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500 @error('email') border-red-400 @enderror">
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700">Teléfono</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500 @error('phone') border-red-400 @enderror">
                                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-slate-700">Mensaje *</label>
                            <textarea name="message" id="message" rows="5" required
                                      class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500 @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="privacy" id="privacy" value="1" required
                                   class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500 @error('privacy') border-red-400 @enderror">
                            <label for="privacy" class="text-sm text-slate-600">
                                He leído y acepto la <a href="{{ route('legal.privacy') }}" class="text-sky-700 underline">política de privacidad</a>. *
                            </label>
                        </div>
                        @error('privacy') <p class="-mt-3 text-sm text-red-600">{{ $message }}</p> @enderror

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-md bg-sky-600 px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-sky-700">
                            Enviar mensaje
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
