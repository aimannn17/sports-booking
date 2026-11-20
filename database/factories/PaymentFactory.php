<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory()->pending(),
            'amount' => 40,
            'method' => PaymentMethod::Cash,
            'reference_no' => null,
        ];
    }

    public function paid(): static
    {
        return $this->afterMaking(function (Payment $payment) {
            $payment->status = PaymentStatus::Paid;
            $payment->paid_at = now();
            $payment->receipt_no = 'RCP-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####');
        });
    }
}
