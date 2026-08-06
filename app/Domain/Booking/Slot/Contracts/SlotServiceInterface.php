<?php

namespace App\Domain\Booking\Slot\Contracts;

use Carbon\CarbonImmutable;

interface SlotServiceInterface
{
    /** @return array<string> */
    public function slotsForDay(CarbonImmutable $day): array;

    /** @return array<string, array<string>> */
    public function slotsForRange(CarbonImmutable $from, CarbonImmutable $to): array;

    public function isAvailable(CarbonImmutable $start): bool;
}
