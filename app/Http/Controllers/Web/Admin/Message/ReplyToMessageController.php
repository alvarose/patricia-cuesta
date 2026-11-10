<?php

namespace App\Http\Controllers\Web\Admin\Message;

use App\Domain\Contact\Message\UseCases\ReplyToContactMessage;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Contact\Message\ReplyToMessageRequest;
use App\Models\Contact\ContactMessage;
use Illuminate\Http\RedirectResponse;

class ReplyToMessageController extends WebController
{
    public function __construct(
        private readonly ReplyToContactMessage $replyToMessage,
    ) {}

    public function __invoke(ReplyToMessageRequest $request, ContactMessage $message): RedirectResponse
    {
        $this->authorize('update', $message);

        $this->replyToMessage->handle($message, $request->body());

        return back();
    }
}
