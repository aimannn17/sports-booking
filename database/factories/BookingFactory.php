<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'time_slot_id' => TimeSlot::factory()->booked(),
            'amount_due' => 0,
            'status' => BookingStatus::Confirmed,
            'payment_deadline' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Booking $booking) {
            $booking->booking_code ??= 'BK-'.Str::upper(Str::random(8));
        });
    }

    public function pending(float $amount = 40): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Pending,
            'amount_due' => $amount,
            'payment_deadline' => now()->addDay(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Cancelled,
            'cancel_reason' => 'Change of plans',
        ])->afterMaking(fn (Booking $booking) => $booking->cancelled_at = now());
    }
}
