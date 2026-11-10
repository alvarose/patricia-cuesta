<?php

namespace App\Domain\Patients\PatientHistory\Enums;

use App\Domain\Core\Shared\Concerns\EnumHelpers;
use App\Models\Patients\Patient;

enum PatientHistoryField: string
{
    use EnumHelpers;

    case Status = Patient::STATUS;
    case Topic = Patient::TOPIC;
    case Notes = Patient::NOTES;

    public function label(): string
    {
        return match ($this) {
            self::Status => 'Estado',
            self::Topic => 'Motivo de consulta',
            self::Notes => 'Notas',
        };
    }
}
