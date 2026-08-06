<?php

namespace App\Domain\Booking\Absence\Contracts;

use App\Models\Booking\Absence;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AbsenceRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): Absence;

    /** @return LengthAwarePaginator<int, Absence> */
    public function list(?int $perPage = 50): LengthAwarePaginator;

    public function findById(int $id): ?Absence;

    /** @param array<string, mixed> $data */
    public function update(Absence $absence, array $data): Absence;

    public function delete(Absence $absence): bool;

    /** @return Collection<int, Absence> */
    public function currentOrUpcoming(): Collection;

    /** @return Collection<int, Absence> */
    public function overlapping(CarbonInterface $from, CarbonInterface $to): Collection;
}
