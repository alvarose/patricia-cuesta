<?php

namespace App\Http\Resources\Booking\Appointment;

use App\Models\Booking\Appointment;
use Illuminate\Http\Request;

/** @mixin Appointment */
class AppointmentWithContactResource extends AppointmentResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'hasPatient' => $this->patient_id !== null,
            'email' => $this->contact_email,
            'phone' => $this->contact_phone,
        ];
    }
}
