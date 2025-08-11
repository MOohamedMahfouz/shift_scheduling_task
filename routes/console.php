<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:expire-pending-shift-requests')->everyMinute();
