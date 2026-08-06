<?php

namespace App\Http\Resources\Patients\Patient;

use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Models\Patients\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Patient */
class PatientResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->fullName(),
            'initials' => $this->initials(),
            'since' => $this->created_at->translatedFormat('M Y'),
            'topic' => ConsultationTopic::labelOrDefault($this->topic),
            'topicValue' => $this->topic?->value,
            'sessions' => $this->appointments_count,
            'next' => $this->nextAppointment?->starts_at->translatedFormat('D j M · H:i'),
            'status' => $this->status->value,
            'statusLabel' => $this->status->label(),
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
        ];
    }
}
