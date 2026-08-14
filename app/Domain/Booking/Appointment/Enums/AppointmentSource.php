<?php

namespace App\Domain\Booking\Appointment\Enums;

use App\Domain\Shared\Concerns\EnumHelpers;

enum AppointmentSource: string
{
    use EnumHelpers;

    case Web = 'web';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Web => 'Desde la web',
            self::Admin => 'Creada por Patricia',
        };
    }
}
