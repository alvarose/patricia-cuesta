<?php

namespace App\Domain\Booking\Appointment\Contracts;

use App\Domain\Booking\Appointment\DTO\AppointmentFilterParams;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface AppointmentRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): Appointment;

    /** @return LengthAwarePaginator<int, Appointment> */
    public function list(?AppointmentFilterParams $filters = null, int $perPage = 50): LengthAwarePaginator;

    public function findById(int $id): ?Appointment;

    /** @param array<string, mixed> $data */
    public function update(Appointment $appointment, array $data): Appointment;

    public function delete(Appointment $appointment): bool;

    /** @return Collection<int, Appointment> */
    public function matching(AppointmentFilterParams $filters): Collection;

    public function countMatching(AppointmentFilterParams $filters): int;

    /** @return Collection<int, Appointment> */
    public function lockBlockingOn(CarbonImmutable $day): Collection;

    /** @return SupportCollection<int, CarbonImmutable> */
    public function startDatesBetween(CarbonImmutable $from, CarbonImmutable $to): SupportCollection;
}
