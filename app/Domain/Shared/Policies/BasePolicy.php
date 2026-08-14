<?php

namespace App\Domain\Shared\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

abstract class BasePolicy
{
    protected function allowClinicStaff(User $user, string $denial): Response
    {
        return $user->exists ? Response::allow() : Response::deny($denial);
    }
}
