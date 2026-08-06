<?php

namespace App\Http\Controllers\Web\Admin\Absence;

use App\Domain\Booking\Absence\UseCases\CreateAbsence;
use App\Domain\Booking\Absence\UseCases\DeleteAbsence;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Booking\Absence\StoreAbsenceRequest;
use App\Models\Booking\Absence;
use Illuminate\Http\RedirectResponse;

class AbsenceController extends WebController
{
    public function __construct(
        private readonly CreateAbsence $createAbsence,
        private readonly DeleteAbsence $deleteAbsence,
    ) {}

    public function store(StoreAbsenceRequest $request): RedirectResponse
    {
        $this->createAbsence->execute($request->toData());

        return back();
    }

    public function destroy(Absence $absence): RedirectResponse
    {
        $this->deleteAbsence->execute($absence);

        return back();
    }
}
