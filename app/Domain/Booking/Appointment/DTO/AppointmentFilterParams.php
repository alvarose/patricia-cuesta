<?php

namespace App\Domain\Booking\Appointment\DTO;

use Carbon\CarbonImmutable;

final readonly class AppointmentFilterParams
{
    public function __construct(
        public ?CarbonImmutable $onDate = null,
        public ?CarbonImmutable $from = null,
        public ?CarbonImmutable $to = null,
        public bool $onlyBlocking = false,
        public bool $excludeCancelled = false,
        public bool $awaitingConfirmation = false,
    ) {}

    public static function forDay(CarbonImmutable $day): self
    {
        return new self(onDate: $day, excludeCancelled: true);
    }

    public static function forWeek(CarbonImmutable $start): self
    {
        return new self(from: $start, to: $start->addDays(6)->endOfDay(), excludeCancelled: true);
    }

    public static function blockingBetween(CarbonImmutable $from, CarbonImmutable $to): self
    {
        return new self(from: $from, to: $to, onlyBlocking: true);
    }

    public static function pending(): self
    {
        return new self(awaitingConfirmation: true);
    }
}
