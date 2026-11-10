<?php

namespace App\Domain\Patients\PatientHistory\Services;

use App\Domain\Patients\PatientHistory\Contracts\PatientHistoryRepositoryInterface;
use App\Domain\Patients\PatientHistory\Contracts\PatientHistoryServiceInterface;
use App\Domain\Patients\PatientHistory\DTO\RecordPatientHistoryData;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientHistory;
use Illuminate\Database\Eloquent\Collection;

final class PatientHistoryService implements PatientHistoryServiceInterface
{
    public function __construct(
        private readonly PatientHistoryRepositoryInterface $history,
    ) {}

    public function record(RecordPatientHistoryData $data): PatientHistory
    {
        return $this->history->create($data->toArray());
    }

    /** @return Collection<int, PatientHistory> */
    public function forPatient(Patient $patient): Collection
    {
        return $this->history->forPatient($patient);
    }
}
