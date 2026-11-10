<?php

namespace App\Domain\Patients\PatientHistory\Contracts;

use App\Domain\Patients\PatientHistory\DTO\RecordPatientHistoryData;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientHistory;
use Illuminate\Database\Eloquent\Collection;

interface PatientHistoryServiceInterface
{
    public function record(RecordPatientHistoryData $data): PatientHistory;

    /** @return Collection<int, PatientHistory> */
    public function forPatient(Patient $patient): Collection;
}
