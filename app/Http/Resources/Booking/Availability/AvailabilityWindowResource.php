<?php

namespace App\Http\Resources\Booking\Availability;

use App\Models\Booking\AvailabilityWindow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AvailabilityWindow */
class AvailabilityWindowResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'start' => $this->startClock(),
            'end' => $this->endClock(),
        ];
    }
}
