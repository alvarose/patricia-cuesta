<?php

namespace App\Http\Requests\Patients\Patient;

use App\Domain\Patients\Patient\DTO\CreatePatientData;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Models\Patients\Patient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Patient::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique(Patient::TABLE, Patient::EMAIL)->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', 'max:40'],
            'topic' => ['nullable', Rule::enum(ConsultationTopic::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', Rule::enum(PatientStatus::class)],
        ];
    }

    public function toData(): CreatePatientData
    {
        return new CreatePatientData(
            firstName: $this->string('first_name')->toString(),
            lastName: $this->filled('last_name') ? $this->string('last_name')->toString() : null,
            email: $this->filled('email') ? $this->string('email')->toString() : null,
            phone: $this->filled('phone') ? $this->string('phone')->toString() : null,
            topic: $this->filled('topic') ? ConsultationTopic::from($this->string('topic')->toString()) : null,
            notes: $this->filled('notes') ? $this->string('notes')->toString() : null,
            status: $this->filled('status')
                ? PatientStatus::from($this->string('status')->toString())
                : PatientStatus::Active,
        );
    }
}
