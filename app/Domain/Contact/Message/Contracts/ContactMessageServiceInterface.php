<?php

namespace App\Domain\Contact\Message\Contracts;

use App\Domain\Contact\Message\DTO\CreateContactMessageData;
use App\Domain\Contact\Message\DTO\UpdateContactMessageData;
use App\Models\Contact\ContactMessage;
use Illuminate\Database\Eloquent\Collection;

interface ContactMessageServiceInterface
{
    public function create(CreateContactMessageData $data): ContactMessage;

    public function update(ContactMessage $message, UpdateContactMessageData $data): ContactMessage;

    public function markAsRead(ContactMessage $message): ContactMessage;

    /** @return Collection<int, ContactMessage> */
    public function all(): Collection;

    /** @return Collection<int, ContactMessage> */
    public function recent(int $limit): Collection;

    public function countUnread(): int;
}
