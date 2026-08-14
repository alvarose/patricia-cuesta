<?php

namespace App\Domain\Contact\Message\Policies;

use App\Domain\Shared\Policies\BasePolicy;
use App\Models\Contact\ContactMessage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class ContactMessagePolicy extends BasePolicy
{
    public function viewAny(User $user): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede leer la bandeja de mensajes.');
    }

    public function update(User $user, ContactMessage $message): Response
    {
        return $this->allowClinicStaff($user, 'Solo la consulta puede responder un mensaje.');
    }
}
