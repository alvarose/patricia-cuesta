<?php

namespace App\Domain\Patients\PatientHistory\Repositories;

use App\Domain\Patients\PatientHistory\Contracts\PatientHistoryRepositoryInterface;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientHistory;
use Illuminate\Database\Eloquent\Collection;

final class PatientHistoryRepository implements PatientHistoryRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): PatientHistory
    {
        return PatientHistory::create($data);
    }

    /** @return Collection<int, PatientHistory> */
    public function forPatient(Patient $patient): Collection
    {
        return PatientHistory::query()
            ->where(PatientHistory::PATIENT_ID, $patient->id)
            ->orderByDesc(PatientHistory::EFFECTIVE_AT)
            ->orderByDesc(PatientHistory::ID)
            ->limit(200)
            ->get();
    }
}
