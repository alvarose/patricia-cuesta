<?php

namespace App\Domain\Booking\AppointmentHistory\Contracts;

use App\Models\Booking\Appointment;
use App\Models\Booking\AppointmentHistory;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentHistoryRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): AppointmentHistory;

    /** @return Collection<int, AppointmentHistory> */
    public function forAppointment(Appointment $appointment): Collection;
}
