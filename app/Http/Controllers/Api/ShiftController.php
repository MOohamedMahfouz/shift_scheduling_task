<?php

namespace App\Http\Controllers\Api;

use App\Data\ShiftData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreShiftRequest;
use App\Repositories\ShiftRepository;

class ShiftController extends Controller
{
    public function __construct(
        protected ShiftRepository $shiftRepository,
    ) {}

    public function store(StoreShiftRequest $request)
    {
        $this->shiftRepository->store(ShiftData::from($request->validated()));

        return response()->json([
            'success' => true,
            'message' => 'Shift created successfully.',
        ]);
    }
}
