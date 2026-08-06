<?php

namespace App\Http\Controllers\Web\Public\Legal;

use App\Http\Controllers\Web\WebController;
use Inertia\Inertia;
use Inertia\Response;

class LegalController extends WebController
{
    public function show(string $section): Response
    {
        return Inertia::render('public/Legal', ['section' => $section]);
    }
}
