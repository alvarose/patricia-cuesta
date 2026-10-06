<?php

namespace App\Http\Requests\Booking\Appointment;

use App\Domain\Booking\Appointment\DTO\ScheduleAppointmentData;
use App\Models\Booking\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Appointment::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'email' => ['required', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'date.required' => 'Elige el día de la cita.',
            'date.date_format' => 'Esa fecha no es válida.',
            'time.required' => 'Elige la hora de la cita.',
            'time.date_format' => 'Esa hora no es válida.',
            'email.required' => 'Hace falta un correo para avisar al paciente.',
            'email.email' => 'Ese correo no parece válido.',
        ];
    }

    public function toData(): ScheduleAppointmentData
    {
        return new ScheduleAppointmentData(
            startsAt: CarbonImmutable::createFromFormat(
                '!Y-m-d H:i',
                $this->string('date')->toString().' '.$this->string('time')->toString(),
            ),
            email: $this->string('email')->toString(),
            notes: $this->filled('notes') ? $this->string('notes')->toString() : null,
        );
    }
}
