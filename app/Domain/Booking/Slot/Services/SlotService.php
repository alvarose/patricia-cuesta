<?php

namespace App\Domain\Booking\Slot\Services;

use App\Domain\Booking\Absence\Contracts\AbsenceServiceInterface;
use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\DTO\AppointmentFilterParams;
use App\Domain\Booking\Availability\Contracts\AvailabilityServiceInterface;
use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use App\Domain\Booking\Slot\Support\BookingSettings;
use App\Domain\Booking\Slot\Support\TimeRange;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Models\Booking\Absence;
use App\Models\Booking\Appointment;
use App\Models\Booking\AvailabilityDay;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class SlotService implements SlotServiceInterface
{
    public function __construct(
        private readonly AvailabilityServiceInterface $availability,
        private readonly AbsenceServiceInterface $absences,
        private readonly AppointmentServiceInterface $appointments,
        private readonly SettingsServiceInterface $settings,
    ) {}

    /** @return array<string> */
    public function slotsForDay(CarbonImmutable $day): array
    {
        return $this->slotsForRange($day, $day)[$day->toDateString()] ?? [];
    }

    /** @return array<string, array<string>> */
    public function slotsForRange(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $booking = $this->settings->booking();

        $from = $from->startOfDay();
        $to = $to->startOfDay()->min($booking->horizonFrom(CarbonImmutable::today()));

        if ($to->lessThan($from)) {
            return [];
        }

        $days = $this->availability->enabledSchedule()->keyBy(AvailabilityDay::WEEKDAY);
        $absences = $this->absences->overlapping($from, $to);
        $taken = $this->appointments->matching(
            AppointmentFilterParams::blockingBetween($from->startOfDay(), $to->endOfDay()),
        );

        $result = [];

        for ($day = $from; $day->lessThanOrEqualTo($to); $day = $day->addDay()) {
            $result[$day->toDateString()] = $this->slotsForSingleDay($day, $days, $absences, $taken, $booking);
        }

        return $result;
    }

    public function isAvailable(CarbonImmutable $start): bool
    {
        return in_array($start->format('H:i'), $this->slotsForDay($start), true);
    }

    /**
     * @param  Collection<int|string, AvailabilityDay>  $days
     * @param  Collection<int, Absence>  $absences
     * @param  Collection<int, Appointment>  $taken
     * @return array<string>
     */
    private function slotsForSingleDay(
        CarbonImmutable $day,
        Collection $days,
        Collection $absences,
        Collection $taken,
        BookingSettings $booking,
    ): array {
        $availabilityDay = $days->get($day->isoWeekday());

        if ($availabilityDay === null) {
            return [];
        }

        if ($absences->contains(fn (Absence $absence): bool => $absence->covers($day))) {
            return [];
        }

        $booked = $taken
            ->filter(fn (Appointment $appointment): bool => $appointment->starts_at->isSameDay($day))
            ->map(fn (Appointment $appointment): TimeRange => $appointment->range());

        $notBefore = $booking->minNoticeAt(now());
        $slots = [];

        foreach ($availabilityDay->windows as $availabilityWindow) {
            $window = $availabilityWindow->rangeOn($day);

            for (
                $cursor = $window->start;
                $booking->sessionRangeFor($cursor)->fitsWithin($window);
                $cursor = $cursor->addMinutes($booking->slotStep())
            ) {
                $session = $booking->sessionRangeFor($cursor);

                if ($cursor->lessThan($notBefore)) {
                    continue;
                }

                if ($booked->contains(fn (TimeRange $range): bool => $session->overlaps($range))) {
                    continue;
                }

                $slots[] = $cursor->format('H:i');
            }
        }

        sort($slots);

        return $slots;
    }
}
