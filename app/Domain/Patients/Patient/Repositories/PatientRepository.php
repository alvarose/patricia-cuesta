<?php

namespace App\Domain\Patients\Patient\Repositories;

use App\Domain\Patients\Patient\Contracts\PatientRepositoryInterface;
use App\Domain\Patients\Patient\DTO\PatientFilterParams;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Models\Patients\Patient;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class PatientRepository implements PatientRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    /** @return LengthAwarePaginator<int, Patient> */
    public function list(?PatientFilterParams $filters = null, int $perPage = 50): LengthAwarePaginator
    {
        return $this->query($filters)->paginate($perPage);
    }

    public function findById(int $id): ?Patient
    {
        return Patient::query()->find($id);
    }

    /** @param array<string, mixed> $data */
    public function update(Patient $patient, array $data): Patient
    {
        $patient->update($data);

        return $patient->refresh();
    }

    public function delete(Patient $patient): bool
    {
        return (bool) $patient->delete();
    }

    public function findByEmail(string $email): ?Patient
    {
        return Patient::query()->where(Patient::EMAIL, Str::lower(trim($email)))->first();
    }

    /** @return Collection<int, Patient> */
    public function matching(PatientFilterParams $filters): Collection
    {
        return $this->query($filters)
            ->withCount('appointments')
            ->with('nextAppointment')
            ->get();
    }

    public function countActive(): int
    {
        return Patient::query()->where(Patient::STATUS, PatientStatus::Active)->count();
    }

    public function countRegisteredSince(CarbonInterface $since): int
    {
        return Patient::query()->where(Patient::CREATED_AT, '>=', $since)->count();
    }

    /** @return Builder<Patient> */
    private function query(?PatientFilterParams $filters): Builder
    {
        $query = Patient::query()->orderBy(Patient::FIRST_NAME);

        if ($filters === null) {
            return $query;
        }

        if ($filters->hasSearch()) {
            $term = $filters->search;

            $query->where(function (Builder $query) use ($term): void {
                $query->where(Patient::FIRST_NAME, 'like', "%{$term}%")
                    ->orWhere(Patient::LAST_NAME, 'like', "%{$term}%")
                    ->orWhereIn(Patient::TOPIC, ConsultationTopic::matching($term));
            });
        }

        if ($filters->hasStatus()) {
            $query->where(Patient::STATUS, $filters->status);
        }

        return $query;
    }
}
