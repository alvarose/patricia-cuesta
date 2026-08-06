<?php

namespace App\Domain\Booking\Appointment\UseCases;

use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Mail\AppointmentConfirmed;
use App\Models\Booking\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class ChangeAppointmentStatus
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
    ) {}

    public function execute(Appointment $appointment, AppointmentStatus $target): Appointment
    {
        $appointment = DB::transaction(
            fn (): Appointment => $this->appointments->transitionTo($appointment, $target),
        );

        if ($target === AppointmentStatus::Confirmed) {
            Mail::to($appointment->contact_email)
                ->send((new AppointmentConfirmed($appointment))->afterCommit());
        }

        return $appointment;
    }
}
