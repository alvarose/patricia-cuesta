<?php

namespace App\Domain\Patients\Patient\UseCases;

use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Domain\Patients\Patient\DTO\CreatePatientData;
use App\Models\Patients\Patient;
use Illuminate\Support\Facades\DB;

final class CreatePatient
{
    public function __construct(
        private readonly PatientServiceInterface $patients,
    ) {}

    public function execute(CreatePatientData $data): Patient
    {
        return DB::transaction(fn (): Patient => $this->patients->create($data));
    }
}
