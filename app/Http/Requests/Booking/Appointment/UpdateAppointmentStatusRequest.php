<?php

namespace App\Http\Requests\Booking\Appointment;

use App\Domain\Booking\Appointment\Enums\AppointmentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(AppointmentStatus::class)],
        ];
    }

    public function target(): AppointmentStatus
    {
        return AppointmentStatus::from($this->string('status')->toString());
    }
}
