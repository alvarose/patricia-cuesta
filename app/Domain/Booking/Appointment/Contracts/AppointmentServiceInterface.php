<?php

namespace App\Domain\Booking\Appointment\Contracts;

use App\Domain\Booking\Appointment\DTO\AppointmentFilterParams;
use App\Domain\Booking\Appointment\DTO\CreateAppointmentData;
use App\Domain\Booking\Appointment\DTO\UpdateAppointmentData;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentServiceInterface
{
    public function create(CreateAppointmentData $data): Appointment;

    public function update(Appointment $appointment, UpdateAppointmentData $data): Appointment;

    public function delete(Appointment $appointment): bool;

    public function transitionTo(Appointment $appointment, AppointmentStatus $target): Appointment;

    /** @return Collection<int, Appointment> */
    public function forDay(CarbonImmutable $day): Collection;

    /** @return Collection<int, Appointment> */
    public function blockingForDay(CarbonImmutable $day): Collection;

    /** @return Collection<int, Appointment> */
    public function awaitingConfirmation(): Collection;

    public function countAwaitingConfirmation(): int;

    public function countBlockingBetween(CarbonImmutable $from, CarbonImmutable $to): int;

    /** @return Collection<int, Appointment> */
    public function matching(AppointmentFilterParams $filters): Collection;

    /** @return array<string, bool> */
    public function busyDatesBetween(CarbonImmutable $from, CarbonImmutable $to): array;
}
