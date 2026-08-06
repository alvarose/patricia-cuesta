<?php

namespace App\Rules;

use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailableSlot implements ValidationRule
{
    public function __construct(
        private readonly SlotServiceInterface $slots,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = CarbonImmutable::createFromFormat('Y-m-d H:i', (string) $value);

        if ($start === null || ! $this->slots->isAvailable($start)) {
            $fail('Ese hueco ya no está disponible. Elige otra hora, por favor.');
        }
    }
}
