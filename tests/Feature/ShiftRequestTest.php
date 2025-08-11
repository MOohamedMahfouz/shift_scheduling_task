<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Department;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Http\Client\Pool;

class ShiftRequestTest extends TestCase
{
    public function test_multiple_employees_requesting_same_shift_simultaneously()
    {
        $department = Department::factory()->create();

        $shift = Shift::create([
            'department_id' => $department->id,
            'start_time' => Carbon::parse('2025-08-11 09:00:00'),
            'end_time' => Carbon::parse('2025-08-11 11:00:00'),
            'max_resources' => 2,
            'max_employees' => 2,
        ]);

        $employees = User::factory()->count(3)->create([
            'role' => RoleEnum::EMPLOYEE->value
        ]);

        $responses = Http::pool(function (Pool $pool) use ($employees, $shift) {
            foreach ($employees as $employee) {
                $pool->post("http://127.0.0.1:8000/api/shifts/{$shift->id}/request", [
                    'employee_id' => $employee->id
                ]);
            }
        });

        $successCount = collect($responses)->filter(fn($res) => $res->status() === 200)->count();
        $failCount = collect($responses)->filter(fn($res) => $res->status() === 400)->count();

        $this->assertEquals(2, $successCount);
        $this->assertEquals(1, $failCount);
    }
}
