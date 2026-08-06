<?php

namespace App\Http\Requests\Booking\Appointment;

use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Rules\AvailableSlot;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function __construct(
        private readonly SlotServiceInterface $slots,
    ) {
        parent::__construct();
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'topic' => ['nullable', Rule::enum(ConsultationTopic::class)],
            'start' => ['required', 'date_format:Y-m-d H:i', new AvailableSlot($this->slots)],
            'privacy' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Necesito tu nombre para poder dirigirme a ti.',
            'email.required' => 'Necesito tu email para confirmarte la cita.',
            'email.email' => 'Ese email no parece válido.',
            'start.required' => 'Elige un día y una hora para la sesión.',
            'start.date_format' => 'La fecha elegida no es válida.',
            'privacy.accepted' => 'Debes aceptar la política de privacidad.',
        ];
    }

    /** @return array{name: string, email: string, phone: string|null} */
    public function contact(): array
    {
        return [
            'name' => $this->string('name')->toString(),
            'email' => $this->string('email')->toString(),
            'phone' => $this->filled('phone') ? $this->string('phone')->toString() : null,
        ];
    }

    public function start(): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('Y-m-d H:i', $this->string('start')->toString());
    }

    public function topic(): ?ConsultationTopic
    {
        return $this->filled('topic')
            ? ConsultationTopic::from($this->string('topic')->toString())
            : null;
    }
}
