<?php

namespace App\Http\Controllers\Api;

use App\Data\EmployeeShiftData;
use App\Data\ShiftData;
use App\Enums\EmployeeShiftStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEmployeeShiftRequest;
use App\Http\Requests\Api\StoreShiftRequest;
use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use App\Repositories\EmployeeShiftRepository;
use App\Repositories\ShiftRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ShiftController extends Controller
{
    public function __construct(
        protected ShiftRepository $shiftRepository,
        protected EmployeeShiftRepository $employeeShiftRepository,
    ) {}

    public function store(StoreShiftRequest $request)
    {
        $validated = $request->validated();

        $shift = $this->shiftRepository->store(ShiftData::from($validated));

        return response()->json([
            'success' => true,
            'message' => 'Shift created successfully.',
            'data' => [
                'shift' => ShiftResource::make($shift->load('department'))
            ]
        ]);
    }

    public function requestSlot(Shift $shift, StoreEmployeeShiftRequest $request)
    {
        try {
            DB::beginTransaction();

            $shift = $this->shiftRepository->lockShift($shift);

            $overlapExists = $this->employeeShiftRepository->overlapExists($shift, $request->employee_id);

            if ($overlapExists) {
                throw new \Exception('Employee has an overlapping shift.', Response::HTTP_BAD_REQUEST);
            }

            if ($shift->employees()->wherePivotIn('status', [EmployeeShiftStatusEnum::APPROVED->value, EmployeeShiftStatusEnum::PENDING->value])->count() >= $shift->max_resources) {
                throw new \Exception('Cannot approve shift request. The maximum number of resources (' . $shift->max_resources . ') has already been allocated for this shift.', Response::HTTP_BAD_REQUEST);
            }

            $this->employeeShiftRepository->store(EmployeeShiftData::from([
                'shift_id' => $shift->id,
                'employee_id' => $request->employee_id,
            ]));

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Slot reserved. Pending approval.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() === Response::HTTP_BAD_REQUEST) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }

            Log::error('Error While requesting a slot: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong!',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
