<?php

namespace App\Domain\Booking\Appointment\Services;

use App\Domain\Booking\Appointment\Contracts\AppointmentRepositoryInterface;
use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\DTO\AppointmentFilterParams;
use App\Domain\Booking\Appointment\DTO\CreateAppointmentData;
use App\Domain\Booking\Appointment\DTO\UpdateAppointmentData;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Booking\Appointment\Exceptions\InvalidAppointmentTransition;
use App\Domain\Booking\AppointmentHistory\Contracts\AppointmentHistoryServiceInterface;
use App\Domain\Booking\AppointmentHistory\DTO\RecordAppointmentHistoryData;
use App\Domain\Booking\AppointmentHistory\Enums\AppointmentHistoryField;
use App\Domain\Core\Shared\Enums\ActionSource;
use App\Models\Booking\Appointment;
use BackedEnum;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class AppointmentService implements AppointmentServiceInterface
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
        private readonly AppointmentHistoryServiceInterface $history,
    ) {}

    public function create(CreateAppointmentData $data): Appointment
    {
        return $this->appointments->create($data->toArray());
    }

    public function update(
        Appointment $appointment,
        UpdateAppointmentData $data,
        ActionSource $source,
        ?Model $actor = null,
    ): Appointment {
        $before = $this->auditedValues($appointment);

        $updated = $this->appointments->update($appointment, $data->toArray());

        foreach ($this->auditedValues($updated) as $field => $value) {
            if ($value === $before[$field]) {
                continue;
            }

            $this->history->record(new RecordAppointmentHistoryData(
                appointmentId: $updated->id,
                field: AppointmentHistoryField::from($field),
                oldValue: $before[$field],
                value: $value,
                source: $source,
                actor: $actor,
            ));
        }

        return $updated;
    }

    public function delete(Appointment $appointment): bool
    {
        return $this->appointments->delete($appointment);
    }

    public function transitionTo(
        Appointment $appointment,
        AppointmentStatus $target,
        ActionSource $source,
        ?Model $actor = null,
    ): Appointment {
        if (! $appointment->status->canTransitionTo($target)) {
            throw InvalidAppointmentTransition::between($appointment->status, $target);
        }

        return $this->update($appointment, UpdateAppointmentData::status($target), $source, $actor);
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

    /** @return array<string, int> */
    public function sessionCountsBetween(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $counts = [];

        foreach ($this->appointments->startDatesBetween($from, $to) as $startsAt) {
            $date = $startsAt->toDateString();
            $counts[$date] = ($counts[$date] ?? 0) + 1;
        }

        return $counts;
    }

    /** @return array<string, ?string> */
    private function auditedValues(Appointment $appointment): array
    {
        $values = [];

        foreach (AppointmentHistoryField::cases() as $field) {
            $values[$field->value] = $this->asText($appointment->getAttribute($field->value));
        }

        return $values;
    }

    private function asText(mixed $value): ?string
    {
        return match (true) {
            $value instanceof BackedEnum => (string) $value->value,
            $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
            is_scalar($value) => (string) $value,
            default => null,
        };
    }
}
