<?php

namespace App\Domain\Patients\Patient\UseCases;

use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Models\Patients\Patient;
use Illuminate\Support\Facades\DB;

final class DeletePatient
{
    public function __construct(
        private readonly PatientServiceInterface $patients,
    ) {}

    public function execute(Patient $patient): bool
    {
        return DB::transaction(fn (): bool => $this->patients->delete($patient));
    }
}
