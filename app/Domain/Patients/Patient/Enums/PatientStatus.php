<?php

namespace App\Domain\Patients\Patient\Enums;

use App\Domain\Shared\Concerns\EnumHelpers;

enum PatientStatus: string
{
    use EnumHelpers;

    case Active = 'active';
    case Paused = 'paused';
    case Discharged = 'discharged';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'En proceso',
            self::Paused => 'En pausa',
            self::Discharged => 'Alta',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Active => 'chip--conf',
            self::Paused => 'chip--pend',
            self::Discharged => 'chip--done',
        };
    }

    /** @return array<array{value: string, label: string}> */
    public static function options(): array
    {
        return array_map(fn (self $status) => [
            'value' => $status->value,
            'label' => $status->label(),
        ], self::cases());
    }
}
