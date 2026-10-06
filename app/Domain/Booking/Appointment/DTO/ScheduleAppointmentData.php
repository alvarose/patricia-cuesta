<?php

namespace App\Domain\Booking\Appointment\DTO;

use Carbon\CarbonImmutable;

final readonly class ScheduleAppointmentData
{
    public function __construct(
        public CarbonImmutable $startsAt,
        public string $email,
        public ?string $notes = null,
    ) {}
}
