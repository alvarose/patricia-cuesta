<?php

namespace App\Domain\Contact\Message\UseCases;

use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Domain\Contact\Message\DTO\UpdateContactMessageData;
use App\Domain\Patients\Patient\Contracts\PatientServiceInterface;
use App\Models\Contact\ContactMessage;
use Illuminate\Support\Facades\DB;

final class LinkPatientToMessage
{
    public function __construct(
        private readonly ContactMessageServiceInterface $messages,
        private readonly PatientServiceInterface $patients,
    ) {}

    public function execute(ContactMessage $message): ContactMessage
    {
        if ($message->patient_id !== null) {
            return $message;
        }

        return DB::transaction(function () use ($message): ContactMessage {
            $patient = $this->patients->findOrCreateFromContact(
                $message->name,
                $message->email,
                $message->phone,
            );

            return $this->messages->update($message, UpdateContactMessageData::patient($patient->id));
        });
    }
}
