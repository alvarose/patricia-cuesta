<?php

namespace App\Domain\Contact\Message\Enums;

enum ContactPreference: string
{
    case Whatsapp = 'whatsapp';
    case Call = 'call';
    case Email = 'email';

    public function label(): string
    {
        return match ($this) {
            self::Whatsapp => 'WhatsApp',
            self::Call => 'Llamada',
            self::Email => 'Email',
        };
    }
}
