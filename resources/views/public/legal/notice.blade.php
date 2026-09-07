<x-layouts.public title="Aviso legal" description="Aviso legal de {{ $settings->company_name }}.">
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Aviso legal</h1>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6 text-slate-700 leading-relaxed">
            <p class="text-sm text-slate-500">
                Este es un texto orientativo. Debe ser revisado y completado con los datos reales de la empresa
                antes de su publicación.
            </p>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">1. Datos identificativos</h2>
                <p class="mt-2">
                    En cumplimiento de la Ley 34/2002 de Servicios de la Sociedad de la Información y Comercio
                    Electrónico (LSSI-CE), se informa de los siguientes datos:
                </p>
                <ul class="mt-3 list-disc pl-6 space-y-1">
                    <li>Titular: {{ $settings->legal_name }}</li>
                    <li>NIF/CIF: {{ $settings->nif }}</li>
                    <li>Domicilio: {{ $settings->address }}, {{ $settings->postal_code }} {{ $settings->city }}</li>
                    <li>Teléfono: {{ $settings->phone }}</li>
                    <li>Email: {{ $settings->email }}</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">2. Objeto</h2>
                <p class="mt-2">
                    El presente sitio web tiene como finalidad ofrecer información sobre los servicios de instalación,
                    reparación y mantenimiento de bombas de agua prestados por {{ $settings->company_name }}.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">3. Propiedad intelectual</h2>
                <p class="mt-2">
                    Todos los contenidos del sitio web (textos, imágenes, logotipos, diseño) son propiedad del titular
                    o cuenta con autorización para su uso, y están protegidos por la normativa de propiedad intelectual.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">4. Responsabilidad</h2>
                <p class="mt-2">
                    El titular no se hace responsable del mal uso que se realice de los contenidos de este sitio web,
                    siendo responsabilidad exclusiva de la persona que accede o los utiliza.
                </p>
            </div>
        </div>
    </section>
</x-layouts.public>
