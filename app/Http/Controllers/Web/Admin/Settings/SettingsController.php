<?php

namespace App\Http\Controllers\Web\Admin\Settings;

use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Clinic\Settings\UseCases\UpdateClinicProfile;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Clinic\Settings\UpdateClinicSettingsRequest;
use App\Http\Resources\Clinic\Settings\ClinicProfileResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends WebController
{
    public function __construct(
        private readonly SettingsServiceInterface $settings,
        private readonly UpdateClinicProfile $updateProfile,
    ) {}

    public function edit(): Response
    {
        return Inertia::render('admin/Settings', [
            'profile' => new ClinicProfileResource($this->settings->profile()),
            'booking' => ['calcom_url' => $this->settings->calcomUrl()],
        ]);
    }

    public function update(UpdateClinicSettingsRequest $request): RedirectResponse
    {
        $this->updateProfile->execute($request->toData());

        return back();
    }
}
