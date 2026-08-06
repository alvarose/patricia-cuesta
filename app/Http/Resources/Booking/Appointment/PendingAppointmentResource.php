<?php

namespace App\Http\Resources\Booking\Appointment;

use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Models\Booking\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Appointment */
class PendingAppointmentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->displayName(),
            'when' => $this->starts_at->translatedFormat('D j M · H:i'),
            'topic' => ConsultationTopic::labelOrDefault($this->topic),
        ];
    }
}
