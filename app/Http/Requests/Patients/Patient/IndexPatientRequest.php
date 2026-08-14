<?php

namespace App\Http\Requests\Patients\Patient;

use App\Domain\Patients\Patient\DTO\PatientFilterParams;
use App\Domain\Patients\Patient\Enums\PatientStatus;
use App\Models\Patients\Patient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexPatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Patient::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in([...array_column(PatientStatus::cases(), 'value'), 'all'])],
        ];
    }

    public function toData(): PatientFilterParams
    {
        $status = $this->string('status')->toString();

        return new PatientFilterParams(
            search: $this->string('search')->trim()->toString(),
            status: $status !== '' ? $status : 'all',
        );
    }
}
