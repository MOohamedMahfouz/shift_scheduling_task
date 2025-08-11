<?php

namespace App\Http\Controllers\Api;

use App\Data\EmployeeShiftData;
use App\Enums\EmployeeShiftStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Repositories\EmployeeShiftRepository;
use App\Repositories\ShiftRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EmployeeShiftController extends Controller
{
    public function __construct(
        protected EmployeeShiftRepository $employeeShiftRepository,
        protected ShiftRepository $shiftRepository,
    ) {}

    public function approveRequest(Shift $shift, EmployeeShift $employeeShift)
    {
        try {
            DB::beginTransaction();

            $shift = $this->shiftRepository->lockShift($shift);

            if ($shift->employees()->wherePivot('status', EmployeeShiftStatusEnum::APPROVED->value)->count() >= $shift->max_resources) {
                throw new \Exception('Cannot approve shift request. The maximum number of resources (' . $shift->max_resources . ') has already been allocated for this shift.');
            }
            $this->employeeShiftRepository->update($employeeShift, EmployeeShiftData::from([
                'approved_at' => now(),
                'status' => EmployeeShiftStatusEnum::APPROVED->value,
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift approved successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() === Response::HTTP_BAD_REQUEST) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }

            Log::error('Error While approving a request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong!',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
