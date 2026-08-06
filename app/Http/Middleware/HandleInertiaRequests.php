<?php

namespace App\Http\Middleware;

use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function __construct(
        private readonly SettingsServiceInterface $settings,
        private readonly AppointmentServiceInterface $appointments,
        private readonly ContactMessageServiceInterface $messages,
    ) {}

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /** @return array<string, mixed> */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'clinic' => fn (): array => $this->clinic(),
            'adminCounts' => $request->user() === null ? null : fn (): array => [
                'pendingAppointments' => $this->appointments->countAwaitingConfirmation(),
                'unreadMessages' => $this->messages->countUnread(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /** @return array{whatsapp: string, email: string, license: string} */
    private function clinic(): array
    {
        $profile = $this->settings->profile();

        return [
            'whatsapp' => $profile->whatsapp,
            'email' => $profile->email,
            'license' => $profile->licenseNumber,
        ];
    }
}
