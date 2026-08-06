<x-mail::message>
# Tu cita está confirmada

Hola {{ $appointment->contact_name }},

Tu sesión con Patricia Cuesta ya está confirmada:

- **Fecha:** {{ $appointment->starts_at->translatedFormat('l j \d\e F') }}
- **Hora:** {{ $appointment->starts_at->format('H:i') }} ({{ $appointment->starts_at->diffInMinutes($appointment->ends_at) }} minutos)
- **Modalidad:** online, por videollamada

Unos minutos antes de la sesión recibirás el enlace para conectarte.
Solo necesitas un espacio tranquilo, conexión a internet y un momento para ti.

Si necesitas cambiar la cita, responde a este email y lo vemos sin problema.

Un abrazo,<br>
Patricia Cuesta
</x-mail::message>
