<?php

use App\Domain\Booking\Calendar\Contracts\MonthCalendarServiceInterface;
use App\Models\Booking\Absence;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Database\Seeders\AvailabilitySeeder;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->calendar = app(MonthCalendarServiceInterface::class);
});

function cellFor(array $days, string $date): array
{
    return collect($days)->firstWhere('date', $date);
}

function monthDays(MonthCalendarServiceInterface $calendar, string $date): array
{
    return $calendar->monthFor(CarbonImmutable::parse($date))->toArray()['days'];
}

it('arranca en lunes y siempre trae 42 celdas', function () {
    $days = monthDays($this->calendar, '2026-03-15');

    expect($days)->toHaveCount(42)
        ->and($days[0]['date'])->toBe('2026-02-23')
        ->and($days[41]['date'])->toBe('2026-04-05');
});

it('no deja celdas vacías cuando el mes ya empieza en lunes', function () {
    $days = monthDays($this->calendar, '2026-06-10');

    expect($days)->toHaveCount(42)
        ->and($days[0]['date'])->toBe('2026-06-01')
        ->and($days[0]['inMonth'])->toBeTrue();
});

it('distingue las celdas de relleno de las del mes', function () {
    $days = monthDays($this->calendar, '2026-03-15');

    expect(cellFor($days, '2026-02-28')['inMonth'])->toBeFalse()
        ->and(cellFor($days, '2026-03-01')['inMonth'])->toBeTrue()
        ->and(cellFor($days, '2026-03-31')['inMonth'])->toBeTrue()
        ->and(cellFor($days, '2026-04-01')['inMonth'])->toBeFalse();
});

it('cuenta las sesiones de cada día y descarta las canceladas', function () {
    $day = CarbonImmutable::parse('2026-03-11');

    Appointment::factory()->count(2)->confirmed()->create([
        Appointment::STARTS_AT => $day->setTime(10, 0),
        Appointment::ENDS_AT => $day->setTime(10, 50),
    ]);
    Appointment::factory()->cancelled()->create([
        Appointment::STARTS_AT => $day->setTime(12, 0),
        Appointment::ENDS_AT => $day->setTime(12, 50),
    ]);
    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => $day->addDay()->setTime(10, 0),
        Appointment::ENDS_AT => $day->addDay()->setTime(10, 50),
    ]);

    $days = monthDays($this->calendar, '2026-03-15');

    expect(cellFor($days, '2026-03-11')['sessions'])->toBe(2)
        ->and(cellFor($days, '2026-03-12')['sessions'])->toBe(1)
        ->and(cellFor($days, '2026-03-13')['sessions'])->toBe(0);
});

it('marca cerrados los días sin consulta', function () {
    $days = monthDays($this->calendar, '2026-03-15');

    expect(cellFor($days, '2026-03-11')['closed'])->toBeFalse()
        ->and(cellFor($days, '2026-03-14')['closed'])->toBeTrue()
        ->and(cellFor($days, '2026-03-15')['closed'])->toBeTrue();
});

it('marca la ausencia en todos sus días, bordes incluidos', function () {
    Absence::factory()->create([
        Absence::STARTS_ON => '2026-03-10',
        Absence::ENDS_ON => '2026-03-12',
    ]);

    $days = monthDays($this->calendar, '2026-03-15');

    expect(cellFor($days, '2026-03-09')['absent'])->toBeFalse()
        ->and(cellFor($days, '2026-03-10')['absent'])->toBeTrue()
        ->and(cellFor($days, '2026-03-11')['absent'])->toBeTrue()
        ->and(cellFor($days, '2026-03-12')['absent'])->toBeTrue()
        ->and(cellFor($days, '2026-03-13')['absent'])->toBeFalse();
});

it('marca también las celdas de relleno que caen dentro de una ausencia', function () {
    Absence::factory()->create([
        Absence::STARTS_ON => '2026-02-27',
        Absence::ENDS_ON => '2026-03-02',
    ]);

    $days = monthDays($this->calendar, '2026-03-15');

    expect(cellFor($days, '2026-02-27')['absent'])->toBeTrue()
        ->and(cellFor($days, '2026-02-28')['absent'])->toBeTrue()
        ->and(cellFor($days, '2026-03-02')['absent'])->toBeTrue()
        ->and(cellFor($days, '2026-02-26')['absent'])->toBeFalse();
});

it('cuenta también las sesiones de las celdas de relleno', function () {
    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => CarbonImmutable::parse('2026-02-24')->setTime(10, 0),
        Appointment::ENDS_AT => CarbonImmutable::parse('2026-02-24')->setTime(10, 50),
    ]);

    expect(cellFor(monthDays($this->calendar, '2026-03-15'), '2026-02-24')['sessions'])->toBe(1);
});

it('navega de mes cruzando el cambio de año', function () {
    $january = $this->calendar->monthFor(CarbonImmutable::parse('2026-01-15'))->toArray();

    expect($january['label'])->toBe('enero 2026')
        ->and($january['prev'])->toBe('2025-12-01')
        ->and($january['next'])->toBe('2026-02-01');
});

it('marca hoy por fecha aunque estés mirando otro mes', function () {
    $this->travelTo(CarbonImmutable::parse('2026-03-05')->setTime(9, 0));

    $days = monthDays($this->calendar, '2026-02-15');

    expect(cellFor($days, '2026-03-05')['today'])->toBeTrue()
        ->and(cellFor($days, '2026-03-04')['today'])->toBeFalse();
});

it('sabe si el mes mostrado es el actual', function () {
    $today = CarbonImmutable::today();

    expect($this->calendar->monthFor($today)->isCurrentMonth)->toBeTrue()
        ->and($this->calendar->monthFor($today->addMonths(2))->isCurrentMonth)->toBeFalse();
});
