<?php

namespace App\Domain\Booking\Availability\Services;

use App\Domain\Booking\Availability\Contracts\AvailabilityRepositoryInterface;
use App\Domain\Booking\Availability\Contracts\AvailabilityServiceInterface;
use App\Domain\Booking\Availability\DTO\UpdateWeeklyScheduleData;
use App\Models\Booking\AvailabilityDay;
use App\Models\Booking\AvailabilityWindow;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

final class AvailabilityService implements AvailabilityServiceInterface
{
    public function __construct(
        private readonly AvailabilityRepositoryInterface $availability,
    ) {}

    /** @return Collection<int, AvailabilityDay> */
    public function weeklySchedule(): Collection
    {
        return $this->availability->allWithWindows();
    }

    /** @return Collection<int, AvailabilityDay> */
    public function enabledSchedule(): Collection
    {
        return $this->availability->enabledWithWindows();
    }

    public function replaceSchedule(UpdateWeeklyScheduleData $data): void
    {
        foreach ($data->days as $schedule) {
            $this->availability->replaceSchedule($schedule);
        }
    }

    public function weeklyMinutes(): int
    {
        $reference = CarbonImmutable::today();

        return $this->enabledSchedule()
            ->flatMap(fn (AvailabilityDay $day) => $day->windows)
            ->sum(fn (AvailabilityWindow $window): int => $window->rangeOn($reference)->minutes());
    }
}
