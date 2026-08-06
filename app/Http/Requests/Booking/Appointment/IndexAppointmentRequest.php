<?php

namespace App\Http\Requests\Booking\Appointment;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    public function selectedDate(): CarbonImmutable
    {
        if (! $this->filled('date')) {
            return CarbonImmutable::today();
        }

        return CarbonImmutable::createFromFormat('!Y-m-d', $this->string('date')->toString());
    }
}
