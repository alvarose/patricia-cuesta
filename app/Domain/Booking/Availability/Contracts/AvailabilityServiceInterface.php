<?php

namespace App\Domain\Booking\Availability\Contracts;

use App\Domain\Booking\Availability\DTO\UpdateWeeklyScheduleData;
use App\Models\Booking\AvailabilityDay;
use Illuminate\Database\Eloquent\Collection;

interface AvailabilityServiceInterface
{
    /** @return Collection<int, AvailabilityDay> */
    public function weeklySchedule(): Collection;

    /** @return Collection<int, AvailabilityDay> */
    public function enabledSchedule(): Collection;

    public function replaceSchedule(UpdateWeeklyScheduleData $data): void;

    public function weeklyMinutes(): int;
}
