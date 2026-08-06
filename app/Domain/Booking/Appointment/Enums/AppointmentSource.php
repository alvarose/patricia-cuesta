<?php

namespace App\Domain\Booking\Appointment\Enums;

enum AppointmentSource: string
{
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
