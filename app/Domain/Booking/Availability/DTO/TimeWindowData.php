<?php

namespace App\Domain\Booking\Availability\DTO;

use App\Domain\Booking\Slot\Support\TimeRange;
use Carbon\CarbonImmutable;

final readonly class TimeWindowData
{
    public function __construct(
        public string $start,
        public string $end,
    ) {}

    public function rangeOn(CarbonImmutable $day): TimeRange
    {
        return TimeRange::onDay($day, $this->start, $this->end);
    }
}
