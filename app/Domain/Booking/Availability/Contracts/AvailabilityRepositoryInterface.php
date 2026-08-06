<?php

namespace App\Domain\Booking\Availability\Contracts;

use App\Domain\Booking\Availability\DTO\WeekdayScheduleData;
use App\Models\Booking\AvailabilityDay;
use Illuminate\Database\Eloquent\Collection;

interface AvailabilityRepositoryInterface
{
    /** @return Collection<int, AvailabilityDay> */
    public function allWithWindows(): Collection;

    /** @return Collection<int, AvailabilityDay> */
    public function enabledWithWindows(): Collection;

    public function findByWeekday(int $weekday): ?AvailabilityDay;

    public function replaceSchedule(WeekdayScheduleData $schedule): AvailabilityDay;
}
