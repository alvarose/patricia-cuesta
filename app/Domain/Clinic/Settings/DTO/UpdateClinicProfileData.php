<?php

namespace App\Domain\Clinic\Settings\DTO;

use Illuminate\Http\UploadedFile;

final readonly class UpdateClinicProfileData
{
    public function __construct(
        public string $name,
        public string $licenseNumber,
        public string $email,
        public string $phone,
        public string $whatsapp,
        public ?UploadedFile $photo = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'profile.name' => $this->name,
            'profile.license_number' => $this->licenseNumber,
            'profile.email' => $this->email,
            'profile.phone' => $this->phone,
            'profile.whatsapp' => $this->whatsapp,
        ];
    }
}
