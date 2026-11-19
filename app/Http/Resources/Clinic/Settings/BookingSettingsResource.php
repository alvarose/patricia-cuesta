<?php

namespace App\Http\Resources\Clinic\Settings;

use App\Domain\Booking\Slot\Support\BookingSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BookingSettings */
final class BookingSettingsResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'sessionMinutes' => $this->sessionMinutes,
            'bufferMinutes' => $this->bufferMinutes,
            'minNoticeHours' => $this->minNoticeHours,
            'maxAdvanceDays' => $this->maxAdvanceDays,
        ];
    }
}
