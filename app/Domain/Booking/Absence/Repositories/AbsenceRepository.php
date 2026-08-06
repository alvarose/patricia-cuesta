<?php

namespace App\Domain\Booking\Absence\Repositories;

use App\Domain\Booking\Absence\Contracts\AbsenceRepositoryInterface;
use App\Models\Booking\Absence;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class AbsenceRepository implements AbsenceRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): Absence
    {
        return Absence::create($data);
    }

    /** @return LengthAwarePaginator<int, Absence> */
    public function list(?int $perPage = 50): LengthAwarePaginator
    {
        return Absence::query()->orderBy(Absence::STARTS_ON)->paginate($perPage);
    }

    public function findById(int $id): ?Absence
    {
        return Absence::query()->find($id);
    }

    /** @param array<string, mixed> $data */
    public function update(Absence $absence, array $data): Absence
    {
        $absence->update($data);

        return $absence->refresh();
    }

    public function delete(Absence $absence): bool
    {
        return (bool) $absence->delete();
    }

    /** @return Collection<int, Absence> */
    public function currentOrUpcoming(): Collection
    {
        return Absence::query()
            ->whereDate(Absence::ENDS_ON, '>=', today())
            ->orderBy(Absence::STARTS_ON)
            ->get();
    }

    /** @return Collection<int, Absence> */
    public function overlapping(CarbonInterface $from, CarbonInterface $to): Collection
    {
        return Absence::query()
            ->whereDate(Absence::STARTS_ON, '<=', $to)
            ->whereDate(Absence::ENDS_ON, '>=', $from)
            ->get();
    }
}
