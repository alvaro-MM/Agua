@php
    $company = config('site.company');
    $contact = config('site.contact');
@endphp

<x-layouts.public title="Política de privacidad" description="Política de privacidad y protección de datos de {{ $company['name'] }}.">
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Política de privacidad</h1>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6 text-slate-700 leading-relaxed">
            <p class="text-sm text-slate-500">
                Texto orientativo conforme al RGPD y la LOPDGDD. Debe revisarse con un asesor antes de publicarse.
            </p>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">1. Responsable del tratamiento</h2>
                <p class="mt-2">
                    {{ $company['legal_name'] }} (NIF {{ $company['nif'] }}), con domicilio en
                    {{ $contact['address'] }}, {{ $contact['postal_code'] }} {{ $company['city'] }}.
                    Email de contacto: {{ $contact['email'] }}.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">2. Finalidad del tratamiento</h2>
                <p class="mt-2">
                    Los datos que nos facilitas a través del formulario de contacto se utilizan únicamente para
                    atender tu solicitud de información o presupuesto y para ponernos en contacto contigo.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">3. Legitimación</h2>
                <p class="mt-2">
                    La base legal es el consentimiento que otorgas al marcar la casilla de aceptación al enviar el
                    formulario.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">4. Conservación de los datos</h2>
                <p class="mt-2">
                    Los datos se conservarán durante el tiempo necesario para atender tu solicitud y, en su caso,
                    mientras exista una relación comercial, salvo que ejerzas tu derecho de supresión.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">5. Derechos</h2>
                <p class="mt-2">
                    Puedes ejercer tus derechos de acceso, rectificación, supresión, oposición, limitación y
                    portabilidad enviando un email a {{ $contact['email'] }}. También puedes reclamar ante la Agencia
                    Española de Protección de Datos (www.aepd.es).
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">6. Destinatarios</h2>
                <p class="mt-2">
                    No se cederán datos a terceros salvo obligación legal.
                </p>
            </div>
        </div>
    </section>
</x-layouts.public>
