<?php

use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\DTO\UpdateAppointmentData;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Booking\Appointment\Exceptions\InvalidAppointmentTransition;
use App\Domain\Booking\Appointment\UseCases\ChangeAppointmentStatus;
use App\Domain\Booking\Appointment\UseCases\LinkPatientToAppointment;
use App\Domain\Booking\AppointmentHistory\Enums\AppointmentHistoryField;
use App\Domain\Core\Shared\Enums\ActionSource;
use App\Models\Booking\Appointment;
use App\Models\Booking\AppointmentHistory;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('registra el cambio de estado con quien lo hizo', function () {
    $appointment = Appointment::factory()->pending()->create();
    $user = User::factory()->create();

    app(ChangeAppointmentStatus::class)->handle(
        $appointment,
        AppointmentStatus::Confirmed,
        ActionSource::Admin,
        $user,
    );

    $row = AppointmentHistory::query()->sole();

    expect($row->field)->toBe(AppointmentHistoryField::Status->value)
        ->and($row->old_value)->toBe('pending')
        ->and($row->value)->toBe('confirmed')
        ->and($row->source)->toBe(ActionSource::Admin)
        ->and($row->actor_type)->toBe('user')
        ->and($row->actor_id)->toBe($user->id);
});

it('registra el cambio de estado sin actor cuando viene de la web', function () {
    $appointment = Appointment::factory()->confirmed()->create();

    app(AppointmentServiceInterface::class)->transitionTo(
        $appointment,
        AppointmentStatus::Cancelled,
        ActionSource::Web,
    );

    $row = AppointmentHistory::query()->sole();

    expect($row->value)->toBe('cancelled')
        ->and($row->source)->toBe(ActionSource::Web)
        ->and($row->actor_type)->toBeNull()
        ->and($row->actor_id)->toBeNull();
});

it('registra el paciente al que se vincula la cita', function () {
    $appointment = Appointment::factory()->create([
        Appointment::PATIENT_ID => null,
        Appointment::CONTACT_NAME => 'Lucía Gómez',
        Appointment::CONTACT_EMAIL => 'lucia@example.com',
    ]);

    $linked = app(LinkPatientToAppointment::class)->handle(
        $appointment,
        ActionSource::Admin,
        User::factory()->create(),
    );

    $row = AppointmentHistory::query()->sole();

    expect($row->field)->toBe(AppointmentHistoryField::PatientId->value)
        ->and($row->old_value)->toBeNull()
        ->and($row->value)->toBe((string) $linked->patient_id);
});

it('no registra nada cuando la transición es imposible', function () {
    $appointment = Appointment::factory()->completed()->create();

    expect(fn () => app(ChangeAppointmentStatus::class)->handle(
        $appointment,
        AppointmentStatus::Confirmed,
        ActionSource::Admin,
        User::factory()->create(),
    ))->toThrow(InvalidAppointmentTransition::class);

    expect($appointment->refresh()->status)->toBe(AppointmentStatus::Completed)
        ->and(AppointmentHistory::query()->count())->toBe(0);
});

it('no registra nada cuando el estado de destino ya es el actual', function () {
    $appointment = Appointment::factory()->confirmed()->create();

    app(AppointmentServiceInterface::class)->update(
        $appointment,
        UpdateAppointmentData::status(AppointmentStatus::Confirmed),
        ActionSource::Admin,
        User::factory()->create(),
    );

    expect(AppointmentHistory::query()->count())->toBe(0);
});
