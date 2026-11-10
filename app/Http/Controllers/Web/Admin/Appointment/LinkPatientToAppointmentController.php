<?php

namespace App\Http\Controllers\Web\Admin\Appointment;

use App\Domain\Booking\Appointment\UseCases\LinkPatientToAppointment;
use App\Domain\Core\Shared\Enums\ActionSource;
use App\Http\Controllers\Web\WebController;
use App\Models\Booking\Appointment;
use App\Models\Patients\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LinkPatientToAppointmentController extends WebController
{
    public function __construct(
        private readonly LinkPatientToAppointment $linkPatient,
    ) {}

    public function __invoke(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('create', Patient::class);

        $this->linkPatient->handle($appointment, ActionSource::Admin, $request->user());

        return back();
    }
}
