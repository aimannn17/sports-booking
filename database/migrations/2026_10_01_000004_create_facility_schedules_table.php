<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0 = Sunday … 6 = Saturday
            $table->time('open_time');
            $table->time('close_time');
            $table->smallInteger('slot_minutes');
            $table->timestamps();

            $table->unique(['facility_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_schedules');
    }
};
