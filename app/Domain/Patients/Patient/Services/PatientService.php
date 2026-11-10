<?php

namespace App\Domain\Patients\Patient\Services;

use App\Domain\Core\Shared\Enums\ActionSource;
use App\Domain\Patients\Patient\Contracts\PatientRepositoryInterface;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\DTO\CreatePatientData;
use App\Domain\Patients\Patient\DTO\PatientFilterParams;
use App\Domain\Patients\Patient\DTO\UpdatePatientData;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\PatientHistory\Contracts\PatientHistoryServiceInterface;
use App\Domain\Patients\PatientHistory\DTO\RecordPatientHistoryData;
use App\Domain\Patients\PatientHistory\Enums\PatientHistoryField;
use App\Models\Patients\Patient;
use BackedEnum;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class PatientService implements PatientServiceInterface
{
    public function __construct(
        private readonly PatientRepositoryInterface $patients,
        private readonly PatientHistoryServiceInterface $history,
    ) {}

    public function create(CreatePatientData $data): Patient
    {
        return $this->patients->create($data->toArray());
    }

    public function update(
        Patient $patient,
        UpdatePatientData $data,
        ActionSource $source,
        ?Model $actor = null,
    ): Patient {
        $before = $this->auditedValues($patient);

        $updated = $this->patients->update($patient, $data->toArray());

        foreach ($this->auditedValues($updated) as $field => $value) {
            if ($value === $before[$field]) {
                continue;
            }

            $this->history->record(new RecordPatientHistoryData(
                patientId: $updated->id,
                field: PatientHistoryField::from($field),
                oldValue: $before[$field],
                value: $value,
                source: $source,
                actor: $actor,
            ));
        }

        return $updated;
    }

    public function delete(Patient $patient): bool
    {
        return $this->patients->delete($patient);
    }

    /** @return Collection<int, Patient> */
    public function matching(PatientFilterParams $filters): Collection
    {
        return $this->patients->matching($filters);
    }

    public function findOrCreateFromContact(
        string $name,
        ?string $email,
        ?string $phone = null,
        ?ConsultationTopic $topic = null,
    ): Patient {
        if ($email !== null && $email !== '') {
            $existing = $this->patients->findByEmail($email);

            if ($existing !== null) {
                return $existing;
            }
        }

        [$firstName, $lastName] = $this->splitName($name);

        return $this->create(new CreatePatientData(
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            phone: $phone,
            topic: $topic,
        ));
    }

    public function countActive(): int
    {
        return $this->patients->countActive();
    }

    public function countRegisteredSince(CarbonInterface $since): int
    {
        return $this->patients->countRegisteredSince($since);
    }

    /** @return array<string, ?string> */
    private function auditedValues(Patient $patient): array
    {
        $values = [];

        foreach (PatientHistoryField::cases() as $field) {
            $values[$field->value] = $this->asText($patient->getAttribute($field->value));
        }

        return $values;
    }

    private function asText(mixed $value): ?string
    {
        return match (true) {
            $value instanceof BackedEnum => (string) $value->value,
            $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
            is_scalar($value) => (string) $value,
            default => null,
        };
    }

    /** @return array{0: string, 1: ?string} */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2) ?: [$name];

        return [$parts[0], $parts[1] ?? null];
    }
}
