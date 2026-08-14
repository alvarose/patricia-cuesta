<?php

namespace App\Domain\Booking\Absence\Policies;

use App\Domain\Shared\Policies\BasePolicy;
use App\Models\Booking\Absence;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class AbsencePolicy extends BasePolicy
{
    public function create(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede bloquear días.');
    }

    public function delete(User $user, Absence $absence): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede liberar un día bloqueado.');
    }
}
