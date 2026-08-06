<?php

namespace App\Domain\Contact\Message\UseCases;

use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Domain\Contact\Message\DTO\CreateContactMessageData;
use App\Mail\ContactMessageReceived;
use App\Models\Contact\ContactMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class SubmitContactMessage
{
    public function __construct(
        private readonly ContactMessageServiceInterface $messages,
        private readonly SettingsServiceInterface $settings,
    ) {}

    public function execute(CreateContactMessageData $data): ContactMessage
    {
        $message = DB::transaction(fn (): ContactMessage => $this->messages->create($data));

        Mail::to($this->settings->profile()->email)
            ->send((new ContactMessageReceived($message))->afterCommit());

        return $message;
    }
}
