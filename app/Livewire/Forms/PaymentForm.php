<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentMethod;
use App\Models\Booking;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PaymentForm extends Form
{
    public string $amount = '';

    public string $method = 'cash';

    public string $reference_no = '';

    protected function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'reference_no' => [
                Rule::requiredIf($this->method === PaymentMethod::BankTransfer->value),
                'nullable', 'string', 'max:50',
            ],
        ];
    }

    protected function messages(): array
    {
        return ['reference_no.required' => 'Enter the bank transfer reference number.'];
    }

    public function forBooking(Booking $booking): void
    {
        $this->reset();
        $this->amount = number_format((float) $booking->amount_due, 2, '.', '');
    }
}
