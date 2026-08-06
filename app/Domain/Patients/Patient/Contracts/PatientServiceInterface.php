<?php

namespace App\Domain\Patients\Patient\Contracts;

use App\Domain\Patients\Patient\DTO\CreatePatientData;
use App\Domain\Patients\Patient\DTO\PatientFilterParams;
use App\Domain\Patients\Patient\DTO\UpdatePatientData;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Models\Patients\Patient;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

interface PatientServiceInterface
{
    public function create(CreatePatientData $data): Patient;

    public function update(Patient $patient, UpdatePatientData $data): Patient;

    public function delete(Patient $patient): bool;

    /** @return Collection<int, Patient> */
    public function matching(PatientFilterParams $filters): Collection;

    public function findOrCreateFromContact(
        string $name,
        ?string $email,
        ?string $phone = null,
        ?ConsultationTopic $topic = null,
    ): Patient;

    public function countActive(): int;

    public function countRegisteredSince(CarbonInterface $since): int;
}
