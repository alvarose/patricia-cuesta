<?php

namespace App\Domain\Booking\Absence\Services;

use App\Domain\Booking\Absence\Contracts\AbsenceRepositoryInterface;
use App\Domain\Booking\Absence\Contracts\AbsenceServiceInterface;
use App\Domain\Booking\Absence\DTO\CreateAbsenceData;
use App\Models\Booking\Absence;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

final class AbsenceService implements AbsenceServiceInterface
{
    public function __construct(
        private readonly AbsenceRepositoryInterface $absences,
    ) {}

    public function create(CreateAbsenceData $data): Absence
    {
        return $this->absences->create($data->toArray());
    }

    public function delete(Absence $absence): bool
    {
        return $this->absences->delete($absence);
    }

    /** @return Collection<int, Absence> */
    public function currentOrUpcoming(): Collection
    {
        return $this->absences->currentOrUpcoming();
    }

    /** @return Collection<int, Absence> */
    public function overlapping(CarbonInterface $from, CarbonInterface $to): Collection
    {
        return $this->absences->overlapping($from, $to);
    }
}
