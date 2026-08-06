<?php

namespace App\Domain\Booking\Absence\Contracts;

use App\Domain\Booking\Absence\DTO\CreateAbsenceData;
use App\Models\Booking\Absence;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

interface AbsenceServiceInterface
{
    public function create(CreateAbsenceData $data): Absence;

    public function delete(Absence $absence): bool;

    /** @return Collection<int, Absence> */
    public function currentOrUpcoming(): Collection;

    /** @return Collection<int, Absence> */
    public function overlapping(CarbonInterface $from, CarbonInterface $to): Collection;
}
