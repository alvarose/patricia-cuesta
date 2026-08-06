<?php

namespace App\Domain\Booking\Appointment\UseCases;

use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Models\Booking\Appointment;
use Illuminate\Support\Facades\DB;

final class DeleteAppointment
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
    ) {}

    public function execute(Appointment $appointment): bool
    {
        return DB::transaction(fn (): bool => $this->appointments->delete($appointment));
    }
}
