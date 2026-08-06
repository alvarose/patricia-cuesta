<?php

namespace App\Domain\Contact\Message\Contracts;

use App\Models\Contact\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContactMessageRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function create(array $data): ContactMessage;

    /** @return LengthAwarePaginator<int, ContactMessage> */
    public function list(?int $perPage = 50): LengthAwarePaginator;

    public function findById(int $id): ?ContactMessage;

    /** @param array<string, mixed> $data */
    public function update(ContactMessage $message, array $data): ContactMessage;

    public function delete(ContactMessage $message): bool;

    /** @return Collection<int, ContactMessage> */
    public function latest(?int $limit = null): Collection;

    public function countUnread(): int;
}
