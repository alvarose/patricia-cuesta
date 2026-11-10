<?php

use App\Domain\Core\Shared\Enums\ActionSource;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\DTO\CreatePatientData;
use App\Domain\Patients\Patient\DTO\PatientFilterParams;
use App\Domain\Patients\Patient\DTO\UpdatePatientData;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Domain\Patients\Patient\UseCases\CreatePatient;
use App\Domain\Patients\Patient\UseCases\DeletePatient;
use App\Domain\Patients\Patient\UseCases\UpdatePatient;
use App\Models\Booking\Appointment;
use App\Models\Patients\Patient;

beforeEach(function () {
    $this->patients = app(PatientServiceInterface::class);
});

it('crea un paciente desde su DTO', function () {
    $patient = app(CreatePatient::class)->handle(new CreatePatientData(
        firstName: 'Lucía',
        lastName: 'Gómez',
        email: 'lucia@example.com',
        topic: ConsultationTopic::Grief,
    ));

    expect($patient->fullName())->toBe('Lucía Gómez')
        ->and($patient->topic)->toBe(ConsultationTopic::Grief)
        ->and($patient->status)->toBe(PatientStatus::Active);
});

it('actualiza y borra un paciente', function () {
    $patient = Patient::factory()->create();

    $updated = app(UpdatePatient::class)->handle(
        $patient,
        new UpdatePatientData(
            firstName: 'Marta',
            status: PatientStatus::Discharged,
        ),
        ActionSource::System,
    );

    expect($updated->first_name)->toBe('Marta')
        ->and($updated->status)->toBe(PatientStatus::Discharged);

    app(DeletePatient::class)->handle($patient);

    expect(Patient::query()->count())->toBe(0);
});

it('parte el nombre en nombre y apellidos', function () {
    $patient = $this->patients->findOrCreateFromContact('Lucía Gómez Pérez', 'lucia@example.com');

    expect($patient->first_name)->toBe('Lucía')
        ->and($patient->last_name)->toBe('Gómez Pérez');
});

it('reutiliza la ficha aunque el email venga con otras mayúsculas', function () {
    $existing = Patient::factory()->create([Patient::EMAIL => 'lucia@example.com']);

    $patient = $this->patients->findOrCreateFromContact('Lucía Gómez', '  Lucia@EXAMPLE.com  ');

    expect($patient->id)->toBe($existing->id)
        ->and(Patient::query()->count())->toBe(1);
});

it('crea fichas distintas para quienes no dejan email', function () {
    $this->patients->findOrCreateFromContact('Lucía Gómez', null);
    $this->patients->findOrCreateFromContact('Marta Ruiz', null);

    expect(Patient::query()->count())->toBe(2);
});

it('busca por nombre y por la etiqueta del motivo', function () {
    Patient::factory()->create([Patient::FIRST_NAME => 'Lucía', Patient::TOPIC => ConsultationTopic::Grief]);
    Patient::factory()->create([Patient::FIRST_NAME => 'Marta', Patient::TOPIC => ConsultationTopic::Anxiety]);

    expect($this->patients->matching(new PatientFilterParams(search: 'Lucía')))->toHaveCount(1)
        ->and($this->patients->matching(new PatientFilterParams(search: 'ansiedad')))->toHaveCount(1);
});

it('filtra por estado', function () {
    Patient::factory()->count(2)->create();
    Patient::factory()->paused()->create();

    expect($this->patients->matching(new PatientFilterParams(status: 'paused')))->toHaveCount(1)
        ->and($this->patients->matching(new PatientFilterParams(status: 'all')))->toHaveCount(3);
});

it('carga la próxima cita que ocupa hueco', function () {
    $patient = Patient::factory()->create();

    Appointment::factory()->forPatient($patient)->completed()->create();
    Appointment::factory()->forPatient($patient)->confirmed()->create([
        Appointment::STARTS_AT => now()->addWeek()->setTime(10, 0),
        Appointment::ENDS_AT => now()->addWeek()->setTime(10, 50),
    ]);

    $found = $this->patients->matching(new PatientFilterParams)->first();

    expect($found->nextAppointment)->not->toBeNull()
        ->and($found->appointments_count)->toBe(2);
});

it('cuenta activos y altas del periodo', function () {
    Patient::factory()->count(3)->create();
    Patient::factory()->paused()->create();

    expect($this->patients->countActive())->toBe(3)
        ->and($this->patients->countRegisteredSince(now()->startOfMonth()))->toBe(4);
});
