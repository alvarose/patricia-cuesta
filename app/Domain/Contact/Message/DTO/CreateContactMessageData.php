<?php

namespace App\Domain\Contact\Message\DTO;

use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Models\Contact\ContactMessage;

final readonly class CreateContactMessageData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public ContactPreference $contactPreference,
        public ?string $preferredTime,
        public string $body,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            ContactMessage::NAME => $this->name,
            ContactMessage::EMAIL => $this->email,
            ContactMessage::PHONE => $this->phone,
            ContactMessage::CONTACT_PREFERENCE => $this->contactPreference,
            ContactMessage::PREFERRED_TIME => $this->preferredTime,
            ContactMessage::BODY => $this->body,
        ];
    }
}
