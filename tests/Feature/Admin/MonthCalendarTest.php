<?php

use App\Models\Booking\Appointment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\AvailabilitySeeder;

beforeEach(function () {
    $this->seed(AvailabilitySeeder::class);
    $this->actingAs(User::factory()->create());
});

it('cuadra el contador de la celda con la lista del día', function () {
    $today = CarbonImmutable::today();

    Appointment::factory()->count(2)->confirmed()->create([
        Appointment::STARTS_AT => $today->setTime(10, 0),
        Appointment::ENDS_AT => $today->setTime(10, 50),
    ]);
    Appointment::factory()->cancelled()->create([
        Appointment::STARTS_AT => $today->setTime(12, 0),
        Appointment::ENDS_AT => $today->setTime(12, 50),
    ]);

    $props = $this->get('/admin/citas')->viewData('page')['props'];
    $cell = collect($props['month']['days'])->firstWhere('date', $today->toDateString());

    expect($cell['sessions'])->toBe(count($props['appointments']));
});

it('sigue contando las sesiones de un día cerrado', function () {
    $saturday = CarbonImmutable::today()->next(CarbonImmutable::SATURDAY);

    Appointment::factory()->confirmed()->create([
        Appointment::STARTS_AT => $saturday->setTime(9, 15),
        Appointment::ENDS_AT => $saturday->setTime(10, 5),
    ]);

    $days = $this->get('/admin/citas?date='.$saturday->toDateString())
        ->viewData('page')['props']['month']['days'];

    $cell = collect($days)->firstWhere('date', $saturday->toDateString());

    expect($cell['closed'])->toBeTrue()
        ->and($cell['sessions'])->toBe(1);
});

it('centra la rejilla en el mes del día pedido', function () {
    $days = $this->get('/admin/citas?date=2026-03-15')
        ->viewData('page')['props']['month'];

    expect($days['label'])->toBe('marzo 2026')
        ->and($days['prev'])->toBe('2026-02-01')
        ->and($days['next'])->toBe('2026-04-01')
        ->and($days['isCurrentMonth'])->toBeFalse();
});
