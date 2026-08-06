<?php

namespace App\Http\Resources\Booking\Availability;

use App\Models\Booking\AvailabilityDay;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AvailabilityDay */
class AvailabilityDayResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'weekday' => $this->weekday,
            'label' => $this->label(),
            'enabled' => $this->is_enabled,
            'windows' => AvailabilityWindowResource::collection($this->whenLoaded('windows')),
        ];
    }
}
