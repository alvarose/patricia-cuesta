<?php

namespace App\Http\Resources\Booking\Absence;

use App\Models\Booking\Absence;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Absence */
class AbsenceResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'range' => $this->formatRange(),
            'note' => $this->note,
        ];
    }

    private function formatRange(): string
    {
        if ($this->isSingleDay()) {
            return $this->starts_on->translatedFormat('j \d\e F');
        }

        return $this->starts_on->translatedFormat('j M').' – '.$this->ends_on->translatedFormat('j M Y');
    }
}
