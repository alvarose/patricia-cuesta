<?php

namespace App\Domain\Contact\Message\Enums;

use App\Domain\Shared\Concerns\EnumHelpers;

enum ContactPreference: string
{
    use EnumHelpers;

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
