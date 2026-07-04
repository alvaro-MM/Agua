@props([
    'title' => null,
    'description' => null,
])

<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $company = config('site.company');
        $contact = config('site.contact');
        $metaTitle = trim((string) $title) !== '' ? $title . ' | ' . $company['name'] : $company['name'] . ' — ' . $company['tagline'];
        $metaDescription = $description ?? $company['description'];
    @endphp

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $company['name'] }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="es_ES">
    <meta name="twitter:card" content="summary_large_image">

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- Fallback sin build: Tailwind 4 y Alpine vía CDN (solo para previsualizar). Ejecutar `npm run build` para producción. --}}
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
        <style>[x-cloak]{display:none !important;}</style>
    @endif

    @stack('head')

    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Plumber',
        'name' => $company['name'],
        'description' => $company['description'],
        'url' => url('/'),
        'telephone' => $contact['phone'],
        'email' => $contact['email'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $contact['address'],
            'postalCode' => $contact['postal_code'],
            'addressLocality' => $company['city'],
            'addressCountry' => 'ES',
        ],
        'areaServed' => $company['service_areas'],
        'openingHours' => 'Mo-Fr 08:00-18:00',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
</head>
<body class="min-h-screen bg-white text-slate-800 antialiased flex flex-col">
    @include('partials.header')

    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('partials.footer')
    @include('partials.whatsapp')
</body>
</html>
