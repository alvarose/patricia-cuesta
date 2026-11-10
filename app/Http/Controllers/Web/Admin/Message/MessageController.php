<?php

namespace App\Http\Controllers\Web\Admin\Message;

use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Http\Controllers\Web\WebController;
use App\Http\Resources\Contact\Message\ContactMessageDetailResource;
use App\Http\Resources\Contact\Message\ContactMessageResource;
use App\Models\Contact\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends WebController
{
    public function __construct(
        private readonly ContactMessageServiceInterface $messages,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ContactMessage::class);

        $messages = $this->messages->all();
        $selectedId = $request->integer('message') ?: $messages->first()?->id;
        $selected = $selectedId !== null ? $messages->firstWhere('id', $selectedId) : null;

        return Inertia::render('admin/Messages', [
            'messages' => ContactMessageResource::collection($messages),
            'selected' => $selected === null ? null : new ContactMessageDetailResource($selected),
        ]);
    }
}
