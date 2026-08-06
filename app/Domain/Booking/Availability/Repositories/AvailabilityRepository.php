<?php

namespace App\Domain\Booking\Availability\Repositories;

use App\Domain\Booking\Availability\Contracts\AvailabilityRepositoryInterface;
use App\Domain\Booking\Availability\DTO\WeekdayScheduleData;
use App\Models\Booking\AvailabilityDay;
use App\Models\Booking\AvailabilityWindow;
use Illuminate\Database\Eloquent\Collection;

final class AvailabilityRepository implements AvailabilityRepositoryInterface
{
    /** @return Collection<int, AvailabilityDay> */
    public function allWithWindows(): Collection
    {
        return AvailabilityDay::query()
            ->with('windows')
            ->orderBy(AvailabilityDay::WEEKDAY)
            ->get();
    }

    /** @return Collection<int, AvailabilityDay> */
    public function enabledWithWindows(): Collection
    {
        return AvailabilityDay::query()
            ->where(AvailabilityDay::IS_ENABLED, true)
            ->with('windows')
            ->orderBy(AvailabilityDay::WEEKDAY)
            ->get();
    }

    public function findByWeekday(int $weekday): ?AvailabilityDay
    {
        return AvailabilityDay::query()->where(AvailabilityDay::WEEKDAY, $weekday)->first();
    }

    public function replaceSchedule(WeekdayScheduleData $schedule): AvailabilityDay
    {
        $day = AvailabilityDay::query()
            ->where(AvailabilityDay::WEEKDAY, $schedule->weekday)
            ->firstOrFail();

        $day->update([AvailabilityDay::IS_ENABLED => $schedule->enabled]);
        $day->windows()->delete();

        foreach (array_values($schedule->windows) as $position => $window) {
            $day->windows()->create([
                AvailabilityWindow::START_TIME => $window->start,
                AvailabilityWindow::END_TIME => $window->end,
                AvailabilityWindow::POSITION => $position,
            ]);
        }

        return $day->refresh();
    }
}
