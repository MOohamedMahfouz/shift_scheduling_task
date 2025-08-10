<?php

use App\Http\Controllers\Api\ShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'shifts',
], function () {

    Route::POST('/', [ShiftController::class, 'store']);
    Route::POST('{shift}/request', [ShiftController::class, 'requestSlot']);


});
