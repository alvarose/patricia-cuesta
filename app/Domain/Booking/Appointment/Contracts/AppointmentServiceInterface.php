<?php

namespace App\Domain\Booking\Appointment\Contracts;

use App\Domain\Booking\Appointment\DTO\AppointmentFilterParams;
use App\Domain\Booking\Appointment\DTO\CreateAppointmentData;
use App\Domain\Booking\Appointment\DTO\UpdateAppointmentData;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Core\Shared\Enums\ActionSource;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AppointmentServiceInterface
{
    public function create(CreateAppointmentData $data): Appointment;

    public function update(
        Appointment $appointment,
        UpdateAppointmentData $data,
        ActionSource $source,
        ?Model $actor = null,
    ): Appointment;

    public function delete(Appointment $appointment): bool;

    public function transitionTo(
        Appointment $appointment,
        AppointmentStatus $target,
        ActionSource $source,
        ?Model $actor = null,
    ): Appointment;

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

    /** @return array<string, int> */
    public function sessionCountsBetween(CarbonImmutable $from, CarbonImmutable $to): array;
}
