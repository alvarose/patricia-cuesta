<?php

use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Booking\Appointment\Exceptions\InvalidAppointmentTransition;
use App\Domain\Booking\Appointment\UseCases\ChangeAppointmentStatus;
use App\Mail\AppointmentConfirmed;
use App\Models\Booking\Appointment;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->changeStatus = app(ChangeAppointmentStatus::class);
});

it('resuelve cada transición posible', function (AppointmentStatus $from, AppointmentStatus $to, bool $allowed) {
    expect($from->canTransitionTo($to))->toBe($allowed);
})->with(function () {
    $matrix = [
        'pending' => ['pending' => false, 'confirmed' => true, 'cancelled' => true, 'completed' => false],
        'confirmed' => ['pending' => false, 'confirmed' => false, 'cancelled' => true, 'completed' => true],
        'cancelled' => ['pending' => false, 'confirmed' => false, 'cancelled' => false, 'completed' => false],
        'completed' => ['pending' => false, 'confirmed' => false, 'cancelled' => false, 'completed' => false],
    ];

    foreach ($matrix as $from => $targets) {
        foreach ($targets as $to => $allowed) {
            yield "{$from} → {$to}" => [AppointmentStatus::from($from), AppointmentStatus::from($to), $allowed];
        }
    }
});

it('confirma una cita pendiente y avisa a quien la pidió', function () {
    Mail::fake();

    $appointment = Appointment::factory()->web()->pending()->create([
        Appointment::CONTACT_EMAIL => 'cliente@example.com',
    ]);

    $this->changeStatus->execute($appointment, AppointmentStatus::Confirmed);

    expect($appointment->refresh()->status)->toBe(AppointmentStatus::Confirmed);
    Mail::assertQueued(
        AppointmentConfirmed::class,
        fn (AppointmentConfirmed $mail) => $mail->hasTo('cliente@example.com'),
    );
});

it('no envía correo automático al rechazar', function () {
    Mail::fake();

    $appointment = Appointment::factory()->web()->pending()->create();

    $this->changeStatus->execute($appointment, AppointmentStatus::Cancelled);

    expect($appointment->refresh()->status)->toBe(AppointmentStatus::Cancelled);
    Mail::assertNothingQueued();
});

it('completa una cita confirmada sin avisar de nuevo', function () {
    Mail::fake();

    $appointment = Appointment::factory()->confirmed()->create();

    $this->changeStatus->execute($appointment, AppointmentStatus::Completed);

    expect($appointment->refresh()->status)->toBe(AppointmentStatus::Completed);
    Mail::assertNothingQueued();
});

it('rechaza una transición imposible sin tocar la cita', function () {
    Mail::fake();

    $appointment = Appointment::factory()->completed()->create();

    expect(fn () => $this->changeStatus->execute($appointment, AppointmentStatus::Confirmed))
        ->toThrow(InvalidAppointmentTransition::class);

    expect($appointment->refresh()->status)->toBe(AppointmentStatus::Completed);
});
