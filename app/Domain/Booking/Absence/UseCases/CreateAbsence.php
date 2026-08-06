<?php

namespace App\Domain\Booking\Absence\UseCases;

use App\Domain\Booking\Absence\Contracts\AbsenceServiceInterface;
use App\Domain\Booking\Absence\DTO\CreateAbsenceData;
use App\Models\Booking\Absence;
use Illuminate\Support\Facades\DB;

final class CreateAbsence
{
    public function __construct(
        private readonly AbsenceServiceInterface $absences,
    ) {}

    public function execute(CreateAbsenceData $data): Absence
    {
        return DB::transaction(fn (): Absence => $this->absences->create($data));
    }
}
