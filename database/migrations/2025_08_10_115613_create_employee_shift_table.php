<?php

use App\Enums\ShiftParticipantStatusEnum;
use App\Models\Shift;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_shift', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained('users');
            $table->foreignIdFor(Shift::class)->constrained();

            $table->enum('status', ShiftParticipantStatusEnum::values())
                ->default(ShiftParticipantStatusEnum::PENDING->value);

            $table->timestamp('reserved_at')->default(now());
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_shift');
    }
};
