<?php

namespace App\Http\Resources\Booking\Appointment;

use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Models\Booking\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Appointment */
class AppointmentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'time' => $this->starts_at->format('H:i'),
            'name' => $this->displayName(),
            'topic' => ConsultationTopic::labelOrDefault($this->topic),
            'status' => $this->status->value,
            'statusLabel' => $this->status->label(),
        ];
    }
}
