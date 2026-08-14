<?php

namespace App\Domain\Booking\Appointment\Policies;

use App\Domain\Shared\Policies\BasePolicy;
use App\Models\Booking\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class AppointmentPolicy extends BasePolicy
{
    public function viewAny(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede ver la agenda.');
    }

    public function update(User $user, Appointment $appointment): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede cambiar el estado de una cita.');
    }

    public function delete(User $user, Appointment $appointment): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede eliminar una cita.');
    }
}
