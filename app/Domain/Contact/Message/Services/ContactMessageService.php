<?php

namespace App\Domain\Contact\Message\Services;

use App\Domain\Contact\Message\Contracts\ContactMessageRepositoryInterface;
use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Domain\Contact\Message\DTO\CreateContactMessageData;
use App\Domain\Contact\Message\DTO\UpdateContactMessageData;
use App\Models\Contact\ContactMessage;
use Illuminate\Database\Eloquent\Collection;

final class ContactMessageService implements ContactMessageServiceInterface
{
    public function __construct(
        private readonly ContactMessageRepositoryInterface $messages,
    ) {}

    public function create(CreateContactMessageData $data): ContactMessage
    {
        return $this->messages->create($data->toArray());
    }

    public function update(ContactMessage $message, UpdateContactMessageData $data): ContactMessage
    {
        return $this->messages->update($message, $data->toArray());
    }

    public function markAsRead(ContactMessage $message): ContactMessage
    {
        if ($message->isRead()) {
            return $message;
        }

        return $this->update($message, UpdateContactMessageData::read(now()));
    }

    /** @return Collection<int, ContactMessage> */
    public function all(): Collection
    {
        return $this->messages->latest();
    }

    /** @return Collection<int, ContactMessage> */
    public function recent(int $limit): Collection
    {
        return $this->messages->latest($limit);
    }

    public function countUnread(): int
    {
        return $this->messages->countUnread();
    }
}
