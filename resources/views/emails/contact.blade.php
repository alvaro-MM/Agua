<x-mail::message>
# Nuevo mensaje de contacto

Has recibido un nuevo mensaje desde el formulario de la web.

**Nombre:** {{ $contactMessage->name }}
**Email:** {{ $contactMessage->email }}
**Teléfono:** {{ $contactMessage->phone ?: 'No indicado' }}
**Fecha:** {{ $contactMessage->created_at?->format('d/m/Y H:i') }}

**Mensaje:**

{{ $contactMessage->message }}

<x-mail::button :url="'mailto:' . $contactMessage->email">
Responder
</x-mail::button>

Gracias,<br>
{{ config('site.company.name') }}
</x-mail::message>
