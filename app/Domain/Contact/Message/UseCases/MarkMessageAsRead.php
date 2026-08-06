<?php

namespace App\Domain\Contact\Message\UseCases;

use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Models\Contact\ContactMessage;
use Illuminate\Support\Facades\DB;

final class MarkMessageAsRead
{
    public function __construct(
        private readonly ContactMessageServiceInterface $messages,
    ) {}

    public function execute(ContactMessage $message): ContactMessage
    {
        return DB::transaction(fn (): ContactMessage => $this->messages->markAsRead($message));
    }
}
