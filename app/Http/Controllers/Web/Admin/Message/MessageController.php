<?php

namespace App\Http\Controllers\Web\Admin\Message;

use App\Domain\Contact\Message\Contracts\ContactMessageServiceInterface;
use App\Domain\Contact\Message\UseCases\LinkPatientToMessage;
use App\Domain\Contact\Message\UseCases\MarkMessageAsRead;
use App\Domain\Contact\Message\UseCases\ReplyToContactMessage;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Contact\Message\ReplyToMessageRequest;
use App\Http\Resources\Contact\Message\ContactMessageDetailResource;
use App\Http\Resources\Contact\Message\ContactMessageResource;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends WebController
{
    public function __construct(
        private readonly ContactMessageServiceInterface $messages,
        private readonly ReplyToContactMessage $replyToMessage,
        private readonly MarkMessageAsRead $markAsRead,
        private readonly LinkPatientToMessage $linkPatient,
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

    public function read(ContactMessage $message): RedirectResponse
    {
        $this->authorize('update', $message);

        $this->markAsRead->execute($message);

        return to_route('admin.messages.index', ['message' => $message->id]);
    }

    public function reply(ReplyToMessageRequest $request, ContactMessage $message): RedirectResponse
    {
        $this->authorize('update', $message);

        $this->replyToMessage->execute($message, $request->body());

        return back();
    }

    public function patient(ContactMessage $message): RedirectResponse
    {
        $this->authorize('create', Patient::class);

        $this->linkPatient->execute($message);

        return back();
    }
}
