<?php

namespace App\Http\Requests\Contact\Message;

use App\Domain\Contact\Message\DTO\CreateContactMessageData;
use App\Domain\Contact\Message\Enums\ContactPreference;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
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
            'contact_preference' => ['required', Rule::enum(ContactPreference::class)],
            'preferred_time' => ['nullable', 'string', 'max:60'],
            'body' => ['required', 'string', 'max:5000'],
            'privacy' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Necesito tu nombre para poder dirigirme a ti.',
            'email.required' => 'Necesito tu email para poder responderte.',
            'email.email' => 'Ese email no parece válido.',
            'body.required' => 'Cuéntame, con tus palabras, qué te gustaría trabajar.',
            'privacy.accepted' => 'Debes aceptar la política de privacidad.',
        ];
    }

    public function toData(): CreateContactMessageData
    {
        return new CreateContactMessageData(
            name: $this->string('name')->toString(),
            email: $this->string('email')->toString(),
            phone: $this->filled('phone') ? $this->string('phone')->toString() : null,
            contactPreference: ContactPreference::from($this->string('contact_preference')->toString()),
            preferredTime: $this->filled('preferred_time') ? $this->string('preferred_time')->toString() : null,
            body: $this->string('body')->toString(),
        );
    }
}
