<?php

namespace App\Http\Resources\Clinic\Settings;

use App\Domain\Clinic\Settings\Support\ClinicProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin ClinicProfile */
class ClinicProfileResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'license_number' => $this->licenseNumber,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'photo_url' => $this->photoPath !== null
                ? Storage::disk('public')->url($this->photoPath)
                : null,
        ];
    }
}
