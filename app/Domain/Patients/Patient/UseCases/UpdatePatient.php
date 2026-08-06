<?php

namespace App\Domain\Patients\Patient\UseCases;

use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\DTO\UpdatePatientData;
use App\Models\Patients\Patient;
use Illuminate\Support\Facades\DB;

final class UpdatePatient
{
    public function __construct(
        private readonly PatientServiceInterface $patients,
    ) {}

    public function execute(Patient $patient, UpdatePatientData $data): Patient
    {
        return DB::transaction(fn (): Patient => $this->patients->update($patient, $data));
    }
}
