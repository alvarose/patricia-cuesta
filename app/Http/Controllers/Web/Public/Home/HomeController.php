<?php

namespace App\Http\Controllers\Web\Public\Home;

use App\Domain\Clinic\Settings\Contracts\SettingsServiceInterface;
use App\Domain\Patients\Patient\Enums\ConsultationTopic;
use App\Http\Controllers\Web\WebController;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends WebController
{
    public function __construct(
        private readonly SettingsServiceInterface $settings,
    ) {}

    public function index(): Response
    {
        return Inertia::render('public/Home', [
            'topics' => ConsultationTopic::options(),
            'sessionMinutes' => $this->settings->booking()->sessionMinutes,
        ]);
    }
}
