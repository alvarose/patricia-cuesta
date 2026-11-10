<?php

use App\Domain\Core\Shared\Enums\ActionSource;
use App\Domain\Patients\Patient\DTO\UpdatePatientData;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Domain\Patients\Patient\UseCases\UpdatePatient;
use App\Domain\Patients\PatientHistory\Contracts\PatientHistoryServiceInterface;
use App\Domain\Patients\PatientHistory\DTO\RecordPatientHistoryData;
use App\Domain\Patients\PatientHistory\Enums\PatientHistoryField;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

function patientWith(ConsultationTopic $topic, ?string $notes, PatientStatus $status): Patient
{
    return Patient::factory()->create([
        Patient::PHONE => '600111222',
        Patient::TOPIC => $topic,
        Patient::NOTES => $notes,
        Patient::STATUS => $status,
    ]);
}

function sameDataWith(Patient $patient, mixed ...$overrides): UpdatePatientData
{
    return new UpdatePatientData(...[
        'firstName' => $patient->first_name,
        'lastName' => $patient->last_name,
        'email' => $patient->email,
        'phone' => $patient->phone,
        'topic' => $patient->topic,
        'notes' => $patient->notes,
        'status' => $patient->status,
        ...$overrides,
    ]);
}

it('registra una fila por cada campo auditado que cambia', function () {
    $patient = patientWith(ConsultationTopic::Grief, 'Primera sesión hecha.', PatientStatus::Active);
    $user = User::factory()->create();

    app(UpdatePatient::class)->handle(
        $patient,
        sameDataWith(
            $patient,
            topic: ConsultationTopic::Anxiety,
            notes: 'Deriva a terapia de pareja.',
            status: PatientStatus::Paused,
        ),
        ActionSource::Admin,
        $user,
    );

    $rows = PatientHistory::query()->get()->keyBy(PatientHistory::FIELD);

    expect($rows)->toHaveCount(3)
        ->and($rows[PatientHistoryField::Topic->value]->old_value)->toBe('duelo')
        ->and($rows[PatientHistoryField::Topic->value]->value)->toBe('ansiedad')
        ->and($rows[PatientHistoryField::Notes->value]->old_value)->toBe('Primera sesión hecha.')
        ->and($rows[PatientHistoryField::Notes->value]->value)->toBe('Deriva a terapia de pareja.')
        ->and($rows[PatientHistoryField::Status->value]->old_value)->toBe('active')
        ->and($rows[PatientHistoryField::Status->value]->value)->toBe('paused');
});

it('no registra nada cuando se guarda la ficha sin cambiar ningún valor', function () {
    $patient = patientWith(ConsultationTopic::Grief, 'Primera sesión hecha.', PatientStatus::Active);

    app(UpdatePatient::class)->handle(
        $patient,
        sameDataWith($patient),
        ActionSource::Admin,
        User::factory()->create(),
    );

    expect(PatientHistory::query()->count())->toBe(0);
});

it('no registra los campos que no se auditan', function () {
    $patient = patientWith(ConsultationTopic::Grief, null, PatientStatus::Active);

    app(UpdatePatient::class)->handle(
        $patient,
        sameDataWith($patient, firstName: 'Marta', phone: '699888777'),
        ActionSource::Admin,
        User::factory()->create(),
    );

    expect($patient->refresh()->phone)->toBe('699888777')
        ->and($patient->first_name)->toBe('Marta')
        ->and(PatientHistory::query()->count())->toBe(0);
});

it('guarda el actor por su alias del morph map, no por el nombre de la clase', function () {
    $patient = patientWith(ConsultationTopic::Grief, null, PatientStatus::Active);
    $user = User::factory()->create();

    app(UpdatePatient::class)->handle(
        $patient,
        sameDataWith($patient, status: PatientStatus::Discharged),
        ActionSource::Admin,
        $user,
    );

    $row = PatientHistory::query()->sole();

    expect($row->actor_type)->toBe('user')
        ->and($row->actor_id)->toBe($user->id)
        ->and($row->actor)->toBeInstanceOf(User::class)
        ->and($row->source)->toBe(ActionSource::Admin);
});

it('registra sin actor cuando el cambio no viene de una persona', function () {
    $patient = patientWith(ConsultationTopic::Grief, null, PatientStatus::Active);

    app(UpdatePatient::class)->handle(
        $patient,
        sameDataWith($patient, status: PatientStatus::Discharged),
        ActionSource::System,
    );

    $row = PatientHistory::query()->sole();

    expect($row->actor_type)->toBeNull()
        ->and($row->actor_id)->toBeNull()
        ->and($row->source)->toBe(ActionSource::System);
});

it('deshace el cambio si su historial no se puede escribir', function () {
    $patient = patientWith(ConsultationTopic::Grief, null, PatientStatus::Active);

    $this->app->instance(PatientHistoryServiceInterface::class, new class implements PatientHistoryServiceInterface
    {
        public function record(RecordPatientHistoryData $data): PatientHistory
        {
            throw new RuntimeException('El historial no se puede escribir.');
        }

        public function forPatient(Patient $patient): Collection
        {
            return new Collection;
        }
    });

    expect(fn () => app(UpdatePatient::class)->handle(
        $patient,
        sameDataWith($patient, status: PatientStatus::Discharged),
        ActionSource::Admin,
        User::factory()->create(),
    ))->toThrow(RuntimeException::class);

    expect(Patient::query()->sole()->status)->toBe(PatientStatus::Active)
        ->and(PatientHistory::query()->count())->toBe(0);
});
