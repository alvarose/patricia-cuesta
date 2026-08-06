<x-mail::message>
# Nuevo mensaje desde la web

**{{ $message->name }}** te ha escrito:

<x-mail::panel>
{{ $message->body }}
</x-mail::panel>

- **Email:** {{ $message->email }}
@if ($message->phone)
- **Teléfono:** {{ $message->phone }}
@endif
- **Prefiere:** {{ $message->contact_preference->label() }}
@if ($message->preferred_time)
- **Horario:** {{ $message->preferred_time }}
@endif

<x-mail::button :url="url('/admin/mensajes')">
Responder desde el panel
</x-mail::button>

Un saludo,<br>
{{ config('app.name') }}
</x-mail::message>
