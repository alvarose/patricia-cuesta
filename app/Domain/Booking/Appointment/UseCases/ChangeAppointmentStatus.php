<?php

namespace App\Domain\Booking\Appointment\UseCases;

use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use App\Domain\Core\Shared\Enums\ActionSource;
use App\Mail\AppointmentConfirmed;
use App\Models\Booking\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class ChangeAppointmentStatus
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
    ) {}

    public function handle(
        Appointment $appointment,
        AppointmentStatus $target,
        ActionSource $source,
        ?Model $actor = null,
    ): Appointment {
        $appointment = DB::transaction(
            fn (): Appointment => $this->appointments->transitionTo($appointment, $target, $source, $actor),
        );

        if ($target === AppointmentStatus::Confirmed) {
            Mail::to($appointment->contact_email)
                ->send((new AppointmentConfirmed($appointment))->afterCommit());
        }

        return $appointment;
    }
}
