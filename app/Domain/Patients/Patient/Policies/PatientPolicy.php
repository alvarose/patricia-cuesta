<?php

namespace App\Domain\Patients\Patient\Policies;

use App\Domain\Shared\Policies\BasePolicy;
use App\Models\Patients\Patient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class PatientPolicy extends BasePolicy
{
    public function viewAny(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede ver el historial de pacientes.');
    }

    public function create(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede dar de alta a un paciente.');
    }

    public function update(User $user, Patient $patient): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede editar la ficha de un paciente.');
    }

    public function delete(User $user, Patient $patient): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede eliminar la ficha de un paciente.');
    }
}
