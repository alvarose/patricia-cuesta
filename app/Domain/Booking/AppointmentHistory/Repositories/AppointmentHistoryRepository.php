<?php

namespace App\Domain\Booking\AppointmentHistory\Repositories;

use App\Domain\Booking\AppointmentHistory\Contracts\AppointmentHistoryRepositoryInterface;
use App\Models\Booking\Appointment;
use App\Models\Booking\AppointmentHistory;
use Illuminate\Database\Eloquent\Collection;

final class AppointmentHistoryRepository implements AppointmentHistoryRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): AppointmentHistory
    {
        return AppointmentHistory::create($data);
    }

    /** @return Collection<int, AppointmentHistory> */
    public function forAppointment(Appointment $appointment): Collection
    {
        return AppointmentHistory::query()
            ->where(AppointmentHistory::APPOINTMENT_ID, $appointment->id)
            ->orderByDesc(AppointmentHistory::EFFECTIVE_AT)
            ->orderByDesc(AppointmentHistory::ID)
            ->limit(200)
            ->get();
    }
}
