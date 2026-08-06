<?php

namespace App\Http\Resources\Contact\Message;

use App\Models\Contact\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ContactMessage */
class ContactMessageDetailResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'when' => $this->created_at->translatedFormat('l j \d\e F · H:i'),
            'email' => $this->email,
            'phone' => $this->phone,
            'preference' => $this->contact_preference->label(),
            'preferredTime' => $this->preferred_time,
            'body' => $this->body,
            'replied' => $this->isReplied(),
            'hasPatient' => $this->patient_id !== null,
        ];
    }
}
