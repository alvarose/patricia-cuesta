<?php

namespace App\Domain\Booking\AppointmentHistory\Contracts;

use App\Domain\Booking\AppointmentHistory\DTO\RecordAppointmentHistoryData;
use App\Models\Booking\Appointment;
use App\Models\Booking\AppointmentHistory;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentHistoryServiceInterface
{
    public function record(RecordAppointmentHistoryData $data): AppointmentHistory;

    /** @return Collection<int, AppointmentHistory> */
    public function forAppointment(Appointment $appointment): Collection;
}
