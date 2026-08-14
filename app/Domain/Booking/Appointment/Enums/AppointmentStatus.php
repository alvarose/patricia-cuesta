<?php

namespace App\Domain\Booking\Appointment\Enums;

use App\Domain\Shared\Concerns\EnumHelpers;

enum AppointmentStatus: string
{
    use EnumHelpers;

    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Confirmed => 'Confirmada',
            self::Cancelled => 'Cancelada',
            self::Completed => 'Completada',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'chip--pend',
            self::Confirmed => 'chip--conf',
            self::Cancelled => 'chip--canc',
            self::Completed => 'chip--done',
        };
    }

    public function dot(): string
    {
        return $this === self::Confirmed ? 'dt--live' : 'dt--idle';
    }

    /** @return list<self> */
    public static function blocking(): array
    {
        return [self::Pending, self::Confirmed];
    }

    /** @return list<self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Completed, self::Cancelled],
            self::Cancelled, self::Completed => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return $target->isA($this->allowedTransitions());
    }
}
