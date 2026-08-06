<?php

namespace App\Domain\Booking\Slot\Support;

use Carbon\CarbonImmutable;

final readonly class TimeRange
{
    public function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
    ) {}

    public static function onDay(CarbonImmutable $day, string $startTime, string $endTime): self
    {
        return new self(
            $day->setTimeFromTimeString($startTime),
            $day->setTimeFromTimeString($endTime),
        );
    }

    public function overlaps(self $other): bool
    {
        return $this->start->lessThan($other->end) && $this->end->greaterThan($other->start);
    }

    public function fitsWithin(self $window): bool
    {
        return $this->start->greaterThanOrEqualTo($window->start)
            && $this->end->lessThanOrEqualTo($window->end);
    }

    public function minutes(): int
    {
        return (int) $this->start->diffInMinutes($this->end);
    }
}
