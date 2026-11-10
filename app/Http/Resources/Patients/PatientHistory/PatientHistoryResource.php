<?php

namespace App\Http\Resources\Patients\PatientHistory;

use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Domain\Patients\PatientHistory\Enums\PatientHistoryField;
use App\Models\Patients\PatientHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PatientHistory */
final class PatientHistoryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $field = PatientHistoryField::from($this->field);

        return [
            'id' => $this->id,
            'field' => $field->label(),
            'from' => $this->readable($field, $this->old_value),
            'to' => $this->readable($field, $this->value),
            'source' => $this->source->label(),
            'actor' => $this->actorName(),
            'at' => $this->effective_at->translatedFormat('j M Y · H:i'),
        ];
    }

    private function readable(PatientHistoryField $field, ?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match ($field) {
            PatientHistoryField::Status => PatientStatus::tryFrom($value)?->label() ?? $value,
            PatientHistoryField::Topic => ConsultationTopic::tryFrom($value)?->label() ?? $value,
            PatientHistoryField::Notes => $value,
        };
    }

    private function actorName(): ?string
    {
        $actor = $this->actor;

        return $actor instanceof User ? $actor->name : null;
    }
}
