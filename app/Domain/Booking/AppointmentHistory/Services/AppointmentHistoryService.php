<?php

namespace App\Domain\Booking\AppointmentHistory\Services;

use App\Domain\Booking\AppointmentHistory\Contracts\AppointmentHistoryRepositoryInterface;
use App\Domain\Booking\AppointmentHistory\Contracts\AppointmentHistoryServiceInterface;
use App\Domain\Booking\AppointmentHistory\DTO\RecordAppointmentHistoryData;
use App\Models\Booking\Appointment;
use App\Models\Booking\AppointmentHistory;
use Illuminate\Database\Eloquent\Collection;

final class AppointmentHistoryService implements AppointmentHistoryServiceInterface
{
    public function __construct(
        private readonly AppointmentHistoryRepositoryInterface $history,
    ) {}

    public function record(RecordAppointmentHistoryData $data): AppointmentHistory
    {
        return $this->history->create($data->toArray());
    }

    /** @return Collection<int, AppointmentHistory> */
    public function forAppointment(Appointment $appointment): Collection
    {
        return $this->history->forAppointment($appointment);
    }
}
