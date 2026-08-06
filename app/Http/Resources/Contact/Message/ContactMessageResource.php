<?php

namespace App\Http\Resources\Contact\Message;

use App\Models\Contact\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ContactMessage */
class ContactMessageResource extends JsonResource
{
    private const PREVIEW_LENGTH = 70;

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'preview' => str($this->body)->limit(self::PREVIEW_LENGTH)->toString(),
            'when' => $this->created_at->diffForHumans(short: true),
            'unread' => ! $this->isRead(),
            'preference' => $this->contact_preference->label(),
        ];
    }
}
