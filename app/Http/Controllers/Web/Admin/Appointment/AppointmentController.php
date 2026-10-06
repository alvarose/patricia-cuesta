<?php

namespace App\Http\Controllers\Web\Admin\Appointment;

use App\Domain\Booking\Appointment\Contracts\AppointmentServiceInterface;
use App\Domain\Booking\Appointment\UseCases\ChangeAppointmentStatus;
use App\Domain\Booking\Appointment\UseCases\DeleteAppointment;
use App\Domain\Booking\Appointment\UseCases\LinkPatientToAppointment;
use App\Domain\Booking\Appointment\UseCases\ScheduleAppointment;
use App\Domain\Booking\Calendar\Contracts\MonthCalendarServiceInterface;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Booking\Appointment\IndexAppointmentRequest;
use App\Http\Requests\Booking\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Booking\Appointment\UpdateAppointmentStatusRequest;
use App\Http\Resources\Booking\Appointment\AppointmentWithContactResource;
use App\Http\Resources\Booking\Appointment\PendingAppointmentResource;
use App\Models\Booking\Appointment;
use App\Models\Patients\Patient;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends WebController
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointments,
        private readonly ChangeAppointmentStatus $changeStatus,
        private readonly LinkPatientToAppointment $linkPatient,
        private readonly DeleteAppointment $deleteAppointment,
        private readonly ScheduleAppointment $scheduleAppointment,
        private readonly MonthCalendarServiceInterface $calendar,
    ) {}

    public function index(IndexAppointmentRequest $request): Response
    {
        $this->authorize('viewAny', Appointment::class);

        $date = $request->selectedDate();

        return Inertia::render('admin/Appointments', [
            'date' => $date->toDateString(),
            'month' => $this->calendar->monthFor($date)->toArray(),
            'appointments' => AppointmentWithContactResource::collection($this->appointments->forDay($date)),
            'pending' => PendingAppointmentResource::collection($this->appointments->awaitingConfirmation()),
        ]);
    }

    public function store(StoreAppointmentRequest $request, Patient $patient): RedirectResponse
    {
        $this->authorize('create', Appointment::class);

        $this->scheduleAppointment->execute($patient, $request->toData());

        return back();
    }

    public function update(UpdateAppointmentStatusRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $this->changeStatus->execute($appointment, $request->target());

        return back();
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $this->authorize('delete', $appointment);

        $this->deleteAppointment->execute($appointment);

        return back();
    }

    public function patient(Appointment $appointment): RedirectResponse
    {
        $this->authorize('create', Patient::class);

        $this->linkPatient->execute($appointment);

        return back();
    }
}
