<?php

namespace App\Http\Requests\Booking\Absence;

use App\Domain\Booking\Absence\DTO\CreateAbsenceData;
use App\Models\Booking\Absence;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Absence::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'ends_on.after_or_equal' => 'La ausencia no puede acabar antes de empezar.',
        ];
    }

    public function toData(): CreateAbsenceData
    {
        return new CreateAbsenceData(
            startsOn: CarbonImmutable::createFromFormat('!Y-m-d', $this->string('starts_on')->toString()),
            endsOn: CarbonImmutable::createFromFormat('!Y-m-d', $this->string('ends_on')->toString()),
            note: $this->filled('note') ? $this->string('note')->toString() : null,
        );
    }
}
