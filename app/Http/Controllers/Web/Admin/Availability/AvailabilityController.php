<?php

namespace App\Http\Controllers\Web\Admin\Availability;

use App\Domain\Booking\Absence\Contracts\AbsenceServiceInterface;
use App\Domain\Booking\Availability\Contracts\AvailabilityServiceInterface;
use App\Domain\Booking\Availability\UseCases\UpdateWeeklySchedule;
use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Booking\Availability\UpdateAvailabilityRequest;
use App\Http\Resources\Booking\Absence\AbsenceResource;
use App\Http\Resources\Booking\Availability\AvailabilityDayResource;
use App\Models\Booking\AvailabilityDay;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AvailabilityController extends WebController
{
    public function __construct(
        private readonly AvailabilityServiceInterface $availability,
        private readonly AbsenceServiceInterface $absences,
        private readonly SettingsServiceInterface $settings,
        private readonly UpdateWeeklySchedule $updateSchedule,
    ) {}

    public function index(): Response
    {
        $days = $this->availability->weeklySchedule();
        $booking = $this->settings->booking();
        $weeklyMinutes = $this->availability->weeklyMinutes();
        $step = $booking->slotStep();

        return Inertia::render('admin/Availability', [
            'days' => AvailabilityDayResource::collection($days),
            'settings' => $booking->toArray(),
            'absences' => AbsenceResource::collection($this->absences->currentOrUpcoming()),
            'stats' => [
                [
                    'label' => 'Días activos',
                    'value' => (string) $days->where(AvailabilityDay::IS_ENABLED, true)->count(),
                    'note' => 'de 7 días a la semana',
                ],
                [
                    'label' => 'Horas semanales',
                    'value' => (string) round($weeklyMinutes / 60, 1),
                    'note' => 'ofrecidas en el calendario',
                ],
                [
                    'label' => 'Huecos por semana',
                    'value' => (string) ($step > 0 ? intdiv($weeklyMinutes, $step) : 0),
                    'note' => sprintf(
                        'sesiones de %d min + %d de pausa',
                        $booking->sessionMinutes,
                        $booking->bufferMinutes,
                    ),
                ],
            ],
        ]);
    }

    public function update(UpdateAvailabilityRequest $request): RedirectResponse
    {
        $this->updateSchedule->execute($request->toData());

        return back();
    }
}
