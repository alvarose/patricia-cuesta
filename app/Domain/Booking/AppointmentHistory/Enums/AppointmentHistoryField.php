<?php

namespace App\Domain\Booking\AppointmentHistory\Enums;

use App\Domain\Core\Shared\Concerns\EnumHelpers;
use App\Models\Booking\Appointment;

enum AppointmentHistoryField: string
{
    use EnumHelpers;

    case Status = Appointment::STATUS;
    case StartsAt = Appointment::STARTS_AT;
    case EndsAt = Appointment::ENDS_AT;
    case PatientId = Appointment::PATIENT_ID;

    public function label(): string
    {
        return match ($this) {
            self::Status => 'Estado',
            self::StartsAt => 'Inicio',
            self::EndsAt => 'Fin',
            self::PatientId => 'Paciente',
        };
    }
}
