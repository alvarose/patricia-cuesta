<?php

namespace App\Domain\Booking\Availability\DTO;

use App\Domain\Booking\Slot\Support\BookingSettings;

final readonly class UpdateWeeklyScheduleData
{
    /** @param array<int, WeekdayScheduleData> $days */
    public function __construct(
        public array $days,
        public BookingSettings $settings,
    ) {}
}
