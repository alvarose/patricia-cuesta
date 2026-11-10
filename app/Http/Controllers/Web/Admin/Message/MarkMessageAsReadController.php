<?php

namespace App\Http\Controllers\Web\Admin\Message;

use App\Domain\Contact\Message\UseCases\MarkMessageAsRead;
use App\Http\Controllers\Web\WebController;
use App\Models\Contact\ContactMessage;
use Illuminate\Http\RedirectResponse;

class MarkMessageAsReadController extends WebController
{
    public function __construct(
        private readonly MarkMessageAsRead $markAsRead,
    ) {}

    public function __invoke(ContactMessage $message): RedirectResponse
    {
        $this->authorize('update', $message);

        $this->markAsRead->handle($message);

        return to_route('admin.messages.index', ['message' => $message->id]);
    }
}
