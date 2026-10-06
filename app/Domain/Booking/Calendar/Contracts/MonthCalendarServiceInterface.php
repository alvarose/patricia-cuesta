<?php

namespace App\Domain\Booking\Calendar\Contracts;

use App\Domain\Booking\Calendar\DTO\MonthCalendarData;
use Carbon\CarbonImmutable;

interface MonthCalendarServiceInterface
{
    public function monthFor(CarbonImmutable $date): MonthCalendarData;
}
