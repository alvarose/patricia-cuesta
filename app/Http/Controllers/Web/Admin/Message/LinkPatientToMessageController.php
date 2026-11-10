<?php

namespace App\Http\Controllers\Web\Admin\Message;

use App\Domain\Contact\Message\UseCases\LinkPatientToMessage;
use App\Http\Controllers\Web\WebController;
use App\Models\Contact\ContactMessage;
use App\Models\Patients\Patient;
use Illuminate\Http\RedirectResponse;

class LinkPatientToMessageController extends WebController
{
    public function __construct(
        private readonly LinkPatientToMessage $linkPatient,
    ) {}

    public function __invoke(ContactMessage $message): RedirectResponse
    {
        $this->authorize('create', Patient::class);

        $this->linkPatient->handle($message);

        return back();
    }
}
