<?php

use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use App\Models\Booking\Absence;
use App\Models\Booking\Appointment;
use App\Models\Booking\AvailabilityDay;
use Carbon\CarbonImmutable;
use Database\Seeders\AvailabilitySeeder;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->slots = app(SlotServiceInterface::class);
    $this->monday = CarbonImmutable::today()->addDays(8)->next(CarbonImmutable::MONDAY);
});

it('genera huecos según franjas, duración y descanso', function () {
    expect($this->slots->slotsForDay($this->monday))
        ->toBe(['10:00', '11:00', '12:00', '13:00', '16:00', '17:00', '18:00', '19:00']);
});

it('devuelve vacío en un día de la semana sin horario', function () {
    expect($this->slots->slotsForDay($this->monday->next(CarbonImmutable::SUNDAY)))->toBe([]);
});

it('devuelve vacío si el día está deshabilitado', function () {
    AvailabilityDay::query()
        ->where(AvailabilityDay::WEEKDAY, 1)
        ->update([AvailabilityDay::IS_ENABLED => false]);

    expect($this->slots->slotsForDay($this->monday))->toBe([]);
});

it('excluye los días cubiertos por una ausencia', function () {
    Absence::factory()->create([
        Absence::STARTS_ON => $this->monday,
        Absence::ENDS_ON => $this->monday->addDay(),
    ]);

    expect($this->slots->slotsForDay($this->monday))->toBe([])
        ->and($this->slots->slotsForDay($this->monday->addDays(2)))->not->toBe([]);
});

it('excluye huecos ocupados por citas pendientes o confirmadas', function () {
    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => $this->monday->setTime(10, 0),
        Appointment::ENDS_AT => $this->monday->setTime(10, 50),
    ]);
    Appointment::factory()->pending()->create([
        Appointment::STARTS_AT => $this->monday->setTime(16, 0),
        Appointment::ENDS_AT => $this->monday->setTime(16, 50),
    ]);

    expect($this->slots->slotsForDay($this->monday))
        ->not->toContain('10:00')
        ->not->toContain('16:00')
        ->toContain('11:00');
});

it('ignora las citas canceladas', function () {
    Appointment::factory()->cancelled()->create([
        Appointment::STARTS_AT => $this->monday->setTime(10, 0),
        Appointment::ENDS_AT => $this->monday->setTime(10, 50),
    ]);

    expect($this->slots->slotsForDay($this->monday))->toContain('10:00');
});

it('respeta la antelación mínima', function () {
    $this->travelTo($this->monday->subDay()->setTime(12, 0));

    $slots = $this->slots->slotsForDay($this->monday);

    expect($slots)->not->toContain('10:00')
        ->not->toContain('11:00')
        ->toContain('12:00')
        ->toContain('13:00');
});

it('no ofrece huecos más allá del horizonte de reserva', function () {
    $far = CarbonImmutable::today()->addDays(90)->next(CarbonImmutable::MONDAY);

    expect($this->slots->slotsForDay($far))->toBe([]);
});

it('valida la disponibilidad de un hueco concreto', function () {
    expect($this->slots->isAvailable($this->monday->setTime(10, 0)))->toBeTrue()
        ->and($this->slots->isAvailable($this->monday->setTime(10, 30)))->toBeFalse();
});
