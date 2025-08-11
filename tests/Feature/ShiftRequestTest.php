<?php

namespace Tests\Feature;

use App\Enums\EmployeeShiftStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Department;
use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Spatie\Async\Pool;


class ShiftRequestTest extends TestCase
{
    public function test_full_flow_with_concurrent_requests_and_approval()
    {
        // Step 1: Manager creates a shift
        $department = Department::factory()->create();

        $shift = Shift::create([
            'department_id' => $department->id,
            'start_time' => Carbon::parse('2025-08-11 09:00:00'),
            'end_time' => Carbon::parse('2025-08-11 11:00:00'),
            'max_resources' => 2,
            'max_employees' => 2,
        ]);

        // Step 2: Prepare employees
        $employees = User::factory()->count(3)->create([
            'role' => RoleEnum::EMPLOYEE->value
        ]);

        // Step 3: Run slot requests concurrently
        $pool = Pool::create();

        foreach ($employees as $employee) {
            $pool->add(function () use ($shift, $employee) {
                return $this->postJson("/api/shifts/{$shift->id}/request", [
                    'employee_id' => $employee->id,
                ])->getStatusCode();
            })->catch(function () {
                return 'error';
            });
        }

        $results = $pool->wait();

        $successCount = collect($results)->filter(fn($status) => $status === 200)->count();
        $failCount = collect($results)->filter(fn($status) => $status === 400)->count();

        $this->assertEquals(2, $successCount);
        $this->assertEquals(1, $failCount);

        // Step 4: Approve one of the pending requests
        $pendingRequest = EmployeeShift::where('shift_id', $shift->id)
            ->where('status', EmployeeShiftStatusEnum::PENDING->value)
            ->first();

        $this->putJson("/api/shifts/{$shift->id}/approve/{$pendingRequest->id}")
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Shift approved successfully.',
            ]);

        $this->assertDatabaseHas('employee_shift', [
            'id' => $pendingRequest->id,
            'status' => EmployeeShiftStatusEnum::APPROVED->value,
        ]);
    }
}
