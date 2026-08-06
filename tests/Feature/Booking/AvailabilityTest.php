<?php

use App\Domain\Booking\Absence\DTO\CreateAbsenceData;
use App\Domain\Booking\Absence\UseCases\CreateAbsence;
use App\Domain\Booking\Absence\UseCases\DeleteAbsence;
use App\Domain\Booking\Availability\Contracts\AvailabilityServiceInterface;
use App\Domain\Booking\Availability\DTO\TimeWindowData;
use App\Domain\Booking\Availability\DTO\UpdateWeeklyScheduleData;
use App\Domain\Booking\Availability\DTO\WeekdayScheduleData;
use App\Domain\Booking\Availability\UseCases\UpdateWeeklySchedule;
use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use App\Domain\Booking\Slot\Support\BookingSettings;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Models\Booking\Absence;
use Carbon\CarbonImmutable;
use Database\Seeders\AvailabilitySeeder;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->monday = CarbonImmutable::today()->addDays(8)->next(CarbonImmutable::MONDAY);
});

function schedule(array $overrides = [], ?BookingSettings $settings = null): UpdateWeeklyScheduleData
{
    $days = [];

    foreach (range(1, 7) as $weekday) {
        $days[] = $overrides[$weekday] ?? new WeekdayScheduleData(
            weekday: $weekday,
            enabled: $weekday <= 5,
            windows: $weekday <= 5
                ? [new TimeWindowData('10:00', '14:00'), new TimeWindowData('16:00', '20:00')]
                : [],
        );
    }

    return new UpdateWeeklyScheduleData($days, $settings ?? new BookingSettings(50, 10, 24, 60));
}

it('reemplaza las franjas de un día', function () {
    app(UpdateWeeklySchedule::class)->execute(schedule([
        1 => new WeekdayScheduleData(1, true, [new TimeWindowData('09:00', '13:00')]),
    ]));

    $monday = app(AvailabilityServiceInterface::class)->weeklySchedule()->firstWhere('weekday', 1);

    expect($monday->windows)->toHaveCount(1)
        ->and($monday->windows->first()->startClock())->toBe('09:00');
});

it('guarda los ajustes de sesión y la caché queda al día', function () {
    app(UpdateWeeklySchedule::class)->execute(schedule(settings: new BookingSettings(60, 0, 24, 60)));

    $booking = app(SettingsServiceInterface::class)->booking();

    expect($booking->sessionMinutes)->toBe(60)
        ->and($booking->slotStep())->toBe(60);
});

it('deja los cambios visibles de inmediato en el calendario público', function () {
    app(UpdateWeeklySchedule::class)->execute(schedule([
        1 => new WeekdayScheduleData(1, false, []),
    ]));

    expect(app(SlotServiceInterface::class)->slotsForDay($this->monday))->toBe([]);
});

it('calcula las horas semanales ofrecidas', function () {
    expect(app(AvailabilityServiceInterface::class)->weeklyMinutes())->toBe(2400);
});

it('las ausencias bloquean el calendario y se pueden eliminar', function () {
    $absence = app(CreateAbsence::class)->execute(new CreateAbsenceData(
        startsOn: $this->monday,
        endsOn: $this->monday,
        note: 'Vacaciones',
    ));

    expect(app(SlotServiceInterface::class)->slotsForDay($this->monday))->toBe([]);

    app(DeleteAbsence::class)->execute($absence);

    expect(Absence::query()->count())->toBe(0)
        ->and(app(SlotServiceInterface::class)->slotsForDay($this->monday))->not->toBe([]);
});
