<?php

namespace App\Domain\Booking\Availability\DTO;

final readonly class WeekdayScheduleData
{
    /** @param array<int, TimeWindowData> $windows */
    public function __construct(
        public int $weekday,
        public bool $enabled,
        public array $windows,
    ) {}
}
