<?php

namespace App\Domain\Contact\Message\UseCases;

use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Domain\Contact\Message\DTO\UpdateContactMessageData;
use App\Domain\Contact\Message\Enums\ContactPreference;
use App\Mail\ContactMessageReply;
use App\Models\Contact\ContactMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class ReplyToContactMessage
{
    public function __construct(
        private readonly ContactMessageServiceInterface $messages,
    ) {}

    public function execute(ContactMessage $message, string $body): ContactMessage
    {
        $message = DB::transaction(fn (): ContactMessage => $this->messages->update(
            $message,
            UpdateContactMessageData::replied(now(), $message->contact_preference),
        ));

        if ($message->contact_preference === ContactPreference::Email) {
            Mail::to($message->email)->send((new ContactMessageReply($message, $body))->afterCommit());
        }

        return $message;
    }
}
