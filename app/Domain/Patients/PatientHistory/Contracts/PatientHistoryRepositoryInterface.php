<?php

namespace App\Domain\Patients\PatientHistory\Contracts;

use App\Models\Patients\Patient;
use App\Models\Patients\PatientHistory;
use Illuminate\Database\Eloquent\Collection;

interface PatientHistoryRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): PatientHistory;

    /** @return Collection<int, PatientHistory> */
    public function forPatient(Patient $patient): Collection;
}
