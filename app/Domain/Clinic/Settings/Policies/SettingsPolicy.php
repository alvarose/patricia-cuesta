<?php

namespace App\Domain\Clinic\Settings\Policies;

use App\Domain\Shared\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class SettingsPolicy extends BasePolicy
{
    public function viewAny(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede ver sus ajustes.');
    }

    public function update(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede cambiar sus ajustes.');
    }
}
