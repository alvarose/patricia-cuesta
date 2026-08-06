<?php

namespace App\Domain\Clinic\Settings\Support;

final readonly class ClinicProfile
{
    public function __construct(
        public string $name,
        public string $licenseNumber,
        public string $email,
        public string $phone,
        public string $whatsapp,
        public ?string $photoPath,
    ) {}
}
