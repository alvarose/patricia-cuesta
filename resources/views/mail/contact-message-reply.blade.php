<x-mail::message>
Hola {{ $message->name }},

{!! nl2br(e($reply)) !!}

Un abrazo,<br>
Patricia Cuesta<br>
<span style="color:#6F6A63; font-size:13px;">Psicóloga sanitaria y forense</span>
</x-mail::message>
