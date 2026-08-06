<?php

namespace App\Domain\Patients\Patient\Services;

use App\Domain\Patients\Patient\Contracts\PatientRepositoryInterface;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\DTO\CreatePatientData;
use App\Domain\Patients\Patient\DTO\PatientFilterParams;
use App\Domain\Patients\Patient\DTO\UpdatePatientData;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Models\Patients\Patient;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

final class PatientService implements PatientServiceInterface
{
    public function __construct(
        private readonly PatientRepositoryInterface $patients,
    ) {}

    public function create(CreatePatientData $data): Patient
    {
        return $this->patients->create($data->toArray());
    }

    public function update(Patient $patient, UpdatePatientData $data): Patient
    {
        return $this->patients->update($patient, $data->toArray());
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

    /** @return array{0: string, 1: ?string} */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2) ?: [$name];

        return [$parts[0], $parts[1] ?? null];
    }
}
