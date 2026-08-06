<?php

namespace App\Http\Controllers\Web\Public\Booking;

use App\Domain\Booking\Appointment\UseCases\BookAppointment;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Booking\Appointment\StoreBookingRequest;
use Illuminate\Http\RedirectResponse;

class BookingController extends WebController
{
    public function __construct(
        private readonly BookAppointment $bookAppointment,
    ) {}

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $appointment = $this->bookAppointment->execute(
            $request->contact(),
            $request->start(),
            $request->topic(),
        );

        return back()->with('booking', [
            'confirmed' => true,
            'start' => $appointment->starts_at->toIso8601String(),
        ]);
    }
}
