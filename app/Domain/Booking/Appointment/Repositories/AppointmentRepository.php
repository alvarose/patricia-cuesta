<?php

namespace App\Domain\Booking\Appointment\Repositories;

use App\Domain\Booking\Appointment\Contracts\AppointmentRepositoryInterface;
use App\Domain\Booking\Appointment\DTO\AppointmentFilterParams;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class AppointmentRepository implements AppointmentRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    /** @return LengthAwarePaginator<int, Appointment> */
    public function list(?AppointmentFilterParams $filters = null, int $perPage = 50): LengthAwarePaginator
    {
        return $this->query($filters)->paginate($perPage);
    }

    public function findById(int $id): ?Appointment
    {
        return Appointment::query()->find($id);
    }

    /** @param array<string, mixed> $data */
    public function update(Appointment $appointment, array $data): Appointment
    {
        $appointment->update($data);

        return $appointment->refresh();
    }

    public function delete(Appointment $appointment): bool
    {
        return (bool) $appointment->delete();
    }

    /** @return Collection<int, Appointment> */
    public function matching(AppointmentFilterParams $filters): Collection
    {
        return $this->query($filters)->with('patient')->get();
    }

    public function countMatching(AppointmentFilterParams $filters): int
    {
        return $this->query($filters)->count();
    }

    /** @return Collection<int, Appointment> */
    public function lockBlockingOn(CarbonImmutable $day): Collection
    {
        return Appointment::query()
            ->whereIn(Appointment::STATUS, AppointmentStatus::blocking())
            ->whereBetween(Appointment::STARTS_AT, [$day->startOfDay(), $day->endOfDay()])
            ->lockForUpdate()
            ->get();
    }

    /** @return SupportCollection<int, CarbonImmutable> */
    public function startDatesBetween(CarbonImmutable $from, CarbonImmutable $to): SupportCollection
    {
        return Appointment::query()
            ->whereNot(Appointment::STATUS, AppointmentStatus::Cancelled)
            ->whereBetween(Appointment::STARTS_AT, [$from, $to])
            ->pluck(Appointment::STARTS_AT);
    }

    /** @return Builder<Appointment> */
    private function query(?AppointmentFilterParams $filters): Builder
    {
        $query = Appointment::query()->orderBy(Appointment::STARTS_AT);

        if ($filters === null) {
            return $query;
        }

        if ($filters->onlyBlocking) {
            $query->whereIn(Appointment::STATUS, AppointmentStatus::blocking());
        }

        if ($filters->excludeCancelled) {
            $query->whereNot(Appointment::STATUS, AppointmentStatus::Cancelled);
        }

        if ($filters->awaitingConfirmation) {
            $query->where(Appointment::STATUS, AppointmentStatus::Pending)
                ->where(Appointment::STARTS_AT, '>=', now());
        }

        if ($filters->onDate !== null) {
            $query->whereDate(Appointment::STARTS_AT, $filters->onDate);
        }

        if ($filters->from !== null && $filters->to !== null) {
            $query->whereBetween(Appointment::STARTS_AT, [$filters->from, $filters->to]);
        }

        return $query;
    }
}
