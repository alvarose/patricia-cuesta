<?php

namespace App\Domain\Booking\Absence\UseCases;

use App\Domain\Booking\Absence\Contracts\AbsenceServiceInterface;
use App\Models\Booking\Absence;
use Illuminate\Support\Facades\DB;

final class DeleteAbsence
{
    public function __construct(
        private readonly AbsenceServiceInterface $absences,
    ) {}

    public function execute(Absence $absence): bool
    {
        return DB::transaction(fn (): bool => $this->absences->delete($absence));
    }
}
