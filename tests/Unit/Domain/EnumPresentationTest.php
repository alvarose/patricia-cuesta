<?php

use App\Domain\Booking\Appointment\Enums\AppointmentSource;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;

it('etiqueta todos los casos de cada enum', function (string $enum) {
    foreach ($enum::cases() as $case) {
        expect($case->label())->not->toBe('');
    }
})->with([
    AppointmentStatus::class,
    AppointmentSource::class,
    PatientStatus::class,
    ConsultationTopic::class,
    ContactPreference::class,
]);

it('pinta el chip de una cita con una clase del design system', function () {
    foreach (AppointmentStatus::cases() as $status) {
        expect($status->badge())->toBeIn(['chip--pend', 'chip--conf', 'chip--done', 'chip--canc']);
    }
});

it('pinta el punto de la agenda con una clase del design system', function () {
    foreach (AppointmentStatus::cases() as $status) {
        expect($status->dot())->toBeIn(['dt--live', 'dt--idle']);
    }

    expect(AppointmentStatus::Confirmed->dot())->toBe('dt--live');
});

it('pinta el chip de un paciente con una clase del design system', function () {
    foreach (PatientStatus::cases() as $status) {
        expect($status->badge())->toBeIn(['chip--pend', 'chip--conf', 'chip--done', 'chip--canc']);
    }
});

it('describe todos los motivos de consulta', function () {
    foreach (ConsultationTopic::cases() as $topic) {
        expect($topic->description())->not->toBe('');
    }
});
