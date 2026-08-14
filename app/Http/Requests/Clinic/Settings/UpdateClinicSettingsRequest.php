<?php

namespace App\Http\Requests\Clinic\Settings;

use App\Domain\Clinic\Settings\DTO\UpdateClinicProfileData;
use App\Models\Clinic\Setting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UpdateClinicSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', Setting::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'calcom_url' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function toData(): UpdateClinicProfileData
    {
        $photo = $this->file('photo');

        return new UpdateClinicProfileData(
            name: $this->string('name')->toString(),
            licenseNumber: $this->string('license_number')->toString(),
            email: $this->string('email')->toString(),
            phone: $this->string('phone')->toString(),
            whatsapp: $this->string('whatsapp')->toString(),
            calcomUrl: $this->filled('calcom_url') ? $this->string('calcom_url')->toString() : null,
            photo: $photo instanceof UploadedFile ? $photo : null,
        );
    }
}
