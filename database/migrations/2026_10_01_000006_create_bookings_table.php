<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 12)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('time_slot_id')->constrained()->restrictOnDelete();
            $table->decimal('amount_due', 6, 2)->default(0);
            $table->string('status', 20)->index();
            $table->dateTime('payment_deadline')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancel_reason', 200)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
