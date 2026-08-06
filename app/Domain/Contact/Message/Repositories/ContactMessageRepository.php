<?php

namespace App\Domain\Contact\Message\Repositories;

use App\Domain\Contact\Message\Contracts\ContactMessageRepositoryInterface;
use App\Models\Contact\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class ContactMessageRepository implements ContactMessageRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): ContactMessage
    {
        return ContactMessage::create($data);
    }

    /** @return LengthAwarePaginator<int, ContactMessage> */
    public function list(?int $perPage = 50): LengthAwarePaginator
    {
        return ContactMessage::query()->latest()->paginate($perPage);
    }

    public function findById(int $id): ?ContactMessage
    {
        return ContactMessage::query()->find($id);
    }

    /** @param array<string, mixed> $data */
    public function update(ContactMessage $message, array $data): ContactMessage
    {
        $message->update($data);

        return $message->refresh();
    }

    public function delete(ContactMessage $message): bool
    {
        return (bool) $message->delete();
    }

    /** @return Collection<int, ContactMessage> */
    public function latest(?int $limit = null): Collection
    {
        $query = ContactMessage::query()->latest();

        if ($limit !== null) {
            $query->take($limit);
        }

        return $query->get();
    }

    public function countUnread(): int
    {
        return ContactMessage::query()->whereNull(ContactMessage::READ_AT)->count();
    }
}
