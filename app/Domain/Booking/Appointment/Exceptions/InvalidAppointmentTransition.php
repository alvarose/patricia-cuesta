<?php

namespace App\Domain\Booking\Appointment\Exceptions;

use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use DomainException;

class InvalidAppointmentTransition extends DomainException
{
    public static function between(AppointmentStatus $from, AppointmentStatus $to): self
    {
        return new self(sprintf(
            'Una cita %s no puede pasar a %s.',
            mb_strtolower($from->label()),
            mb_strtolower($to->label()),
        ));
    }
}
