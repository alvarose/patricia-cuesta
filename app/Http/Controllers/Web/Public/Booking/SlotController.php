<?php

namespace App\Http\Controllers\Web\Public\Booking;

use App\Domain\Booking\Slot\Contracts\SlotServiceInterface;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Booking\Slot\IndexSlotRequest;
use Illuminate\Http\JsonResponse;

class SlotController extends WebController
{
    public function __construct(
        private readonly SlotServiceInterface $slots,
    ) {}

    public function index(IndexSlotRequest $request): JsonResponse
    {
        return response()->json($this->slots->slotsForRange($request->from(), $request->to()));
    }
}
