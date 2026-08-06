<?php

namespace App\Domain\Patients\Patient\Contracts;

use App\Domain\Patients\Patient\DTO\PatientFilterParams;
use App\Models\Patients\Patient;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PatientRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): Patient;

    /** @return LengthAwarePaginator<int, Patient> */
    public function list(?PatientFilterParams $filters = null, int $perPage = 50): LengthAwarePaginator;

    public function findById(int $id): ?Patient;

    /** @param array<string, mixed> $data */
    public function update(Patient $patient, array $data): Patient;

    public function delete(Patient $patient): bool;

    public function findByEmail(string $email): ?Patient;

    /** @return Collection<int, Patient> */
    public function matching(PatientFilterParams $filters): Collection;

    public function countActive(): int;

    public function countRegisteredSince(CarbonInterface $since): int;
}
