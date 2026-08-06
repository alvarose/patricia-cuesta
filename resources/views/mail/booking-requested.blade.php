<x-mail::message>
# Nueva solicitud de cita

**{{ $appointment->contact_name }}** ha pedido una cita desde la web.

- **Fecha:** {{ $appointment->starts_at->translatedFormat('l j \d\e F, H:i') }}
- **Email:** {{ $appointment->contact_email }}
@if ($appointment->contact_phone)
- **Teléfono:** {{ $appointment->contact_phone }}
@endif
@if ($appointment->topic)
- **Motivo:** {{ $appointment->topic->label() }}
@endif

<x-mail::button :url="url('/admin/citas')">
Revisar en el panel
</x-mail::button>

Un saludo,<br>
{{ config('app.name') }}
</x-mail::message>
