<?php

namespace App\Domain\Clinic\Settings\UseCases;

use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Clinic\Settings\DTO\UpdateClinicProfileData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class UpdateClinicProfile
{
    public function __construct(
        private readonly SettingsServiceInterface $settings,
    ) {}

    public function execute(UpdateClinicProfileData $data): void
    {
        $previousPhoto = $this->settings->profile()->photoPath;
        $photoPath = $data->photo?->store('profile', 'public') ?: null;

        DB::transaction(fn () => $this->settings->updateProfile($data, $photoPath));

        if ($photoPath !== null && $previousPhoto !== null) {
            Storage::disk('public')->delete($previousPhoto);
        }
    }
}
