<?php

namespace App\Domain\Booking\Calendar\Services;

use App\Domain\Booking\Absence\Contracts\AbsenceServiceInterface;
use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Availability\Contracts\AvailabilityServiceInterface;
use App\Domain\Booking\Calendar\Contracts\MonthCalendarServiceInterface;
use App\Domain\Booking\Calendar\DTO\MonthCalendarData;
use App\Domain\Booking\Calendar\DTO\MonthDayData;
use App\Models\Booking\Absence;
use App\Models\Booking\AvailabilityDay;
use Carbon\CarbonImmutable;

final class MonthCalendarService implements MonthCalendarServiceInterface
{
    private const CELLS = 42;

    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
        private readonly AbsenceServiceInterface $absences,
        private readonly AvailabilityServiceInterface $availability,
    ) {}

    public function monthFor(CarbonImmutable $date): MonthCalendarData
    {
        $monthStart = $date->startOfMonth();
        $gridStart = $monthStart->subDays($monthStart->isoWeekday() - 1);
        $gridEnd = $gridStart->addDays(self::CELLS - 1);

        $counts = $this->appointments->sessionCountsBetween($gridStart->startOfDay(), $gridEnd->endOfDay());
        $absences = $this->absences->overlapping($gridStart, $gridEnd);
        $openWeekdays = $this->availability->enabledSchedule()->keyBy(AvailabilityDay::WEEKDAY);

        $today = CarbonImmutable::today();

        $days = array_map(function (int $offset) use ($gridStart, $monthStart, $counts, $absences, $openWeekdays, $today): MonthDayData {
            $day = $gridStart->addDays($offset)->startOfDay();
            $key = $day->toDateString();

            return new MonthDayData(
                date: $key,
                num: $day->day,
                sessions: $counts[$key] ?? 0,
                closed: $openWeekdays->get($day->isoWeekday()) === null,
                absent: $absences->contains(fn (Absence $absence): bool => $absence->covers($day)),
                today: $day->isSameDay($today),
                inMonth: $day->month === $monthStart->month && $day->year === $monthStart->year,
            );
        }, range(0, self::CELLS - 1));

        return new MonthCalendarData(
            label: $monthStart->translatedFormat('F Y'),
            prev: $monthStart->subMonth()->toDateString(),
            next: $monthStart->addMonth()->toDateString(),
            today: $today->toDateString(),
            isCurrentMonth: $monthStart->isSameMonth($today),
            days: $days,
        );
    }
}
