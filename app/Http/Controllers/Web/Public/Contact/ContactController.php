<?php

namespace App\Http\Controllers\Web\Public\Contact;

use App\Domain\Contact\Message\UseCases\SubmitContactMessage;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Contact\Message\StoreContactMessageRequest;
use Illuminate\Http\RedirectResponse;

class ContactController extends WebController
{
    public function __construct(
        private readonly SubmitContactMessage $submitMessage,
    ) {}

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $this->submitMessage->execute($request->toData());

        return back()->with('contact', ['sent' => true]);
    }
}
