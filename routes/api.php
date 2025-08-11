<?php

use App\Http\Controllers\Api\EmployeeShiftController;
use App\Http\Controllers\Api\ShiftController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'shifts',
], function () {

    Route::POST('/', [ShiftController::class, 'store']);
    Route::POST('{shift}/request', [ShiftController::class, 'requestSlot']);
    Route::PUT('{shift}/approve/{employeeShift}', [EmployeeShiftController::class, 'approveRequest']);
});
