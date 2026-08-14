<?php

namespace App\Domain\Booking\Availability\Policies;

use App\Domain\Shared\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class AvailabilityPolicy extends BasePolicy
{
    public function viewAny(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede ver su disponibilidad.');
    }

    public function update(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede cambiar su disponibilidad.');
    }
}
