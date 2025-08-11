<?php

namespace App\Console\Commands;

use App\Enums\EmployeeShiftStatusEnum;
use App\Models\EmployeeShift;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpirePendingShiftRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-pending-shift-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reject expired shift requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = EmployeeShift::where('status', EmployeeShiftStatusEnum::PENDING->value)
            ->where('reserved_at', '<', Carbon::now()->subMinutes(5))
            ->update(['status' => EmployeeShiftStatusEnum::EXPIRED->value]);

        $this->info("Rejected {$count} expired shift requests.");
    }
}
