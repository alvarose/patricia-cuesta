<?php

namespace App\Domain\Booking\Appointment\Services;

use App\Domain\Booking\Appointment\Contracts\AppointmentRepositoryInterface;
use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\DTO\AppointmentFilterParams;
use App\Domain\Booking\Appointment\DTO\CreateAppointmentData;
use App\Domain\Booking\Appointment\DTO\UpdateAppointmentData;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Booking\Appointment\Exceptions\InvalidAppointmentTransition;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

final class AppointmentService implements AppointmentServiceInterface
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
    ) {}

    public function create(CreateAppointmentData $data): Appointment
    {
        return $this->appointments->create($data->toArray());
    }

    public function update(Appointment $appointment, UpdateAppointmentData $data): Appointment
    {
        return $this->appointments->update($appointment, $data->toArray());
    }

    public function delete(Appointment $appointment): bool
    {
        return $this->appointments->delete($appointment);
    }

    public function transitionTo(Appointment $appointment, AppointmentStatus $target): Appointment
    {
        if (! $appointment->status->canTransitionTo($target)) {
            throw InvalidAppointmentTransition::between($appointment->status, $target);
        }

        return $this->update($appointment, UpdateAppointmentData::status($target));
    }

    /** @return Collection<int, Appointment> */
    public function forDay(CarbonImmutable $day): Collection
    {
        return $this->appointments->matching(AppointmentFilterParams::forDay($day));
    }

    /** @return Collection<int, Appointment> */
    public function blockingForDay(CarbonImmutable $day): Collection
    {
        return $this->appointments->matching(
            new AppointmentFilterParams(onDate: $day, onlyBlocking: true),
        );
    }

    /** @return Collection<int, Appointment> */
    public function awaitingConfirmation(): Collection
    {
        return $this->appointments->matching(AppointmentFilterParams::pending());
    }

    public function countAwaitingConfirmation(): int
    {
        return $this->appointments->countMatching(AppointmentFilterParams::pending());
    }

    public function countBlockingBetween(CarbonImmutable $from, CarbonImmutable $to): int
    {
        return $this->appointments->countMatching(
            AppointmentFilterParams::blockingBetween($from, $to),
        );
    }

    /** @return Collection<int, Appointment> */
    public function matching(AppointmentFilterParams $filters): Collection
    {
        return $this->appointments->matching($filters);
    }

    /** @return array<string, bool> */
    public function busyDatesBetween(CarbonImmutable $from, CarbonImmutable $to): array
    {
        return $this->appointments->startDatesBetween($from, $to)
            ->mapWithKeys(fn (CarbonImmutable $startsAt): array => [$startsAt->toDateString() => true])
            ->all();
    }
}
