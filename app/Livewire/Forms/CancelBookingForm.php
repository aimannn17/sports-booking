<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class CancelBookingForm extends Form
{
    public ?int $booking_id = null;

    public string $reason = '';

    protected function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer'],
            'reason' => ['nullable', 'string', 'max:200'],
        ];
    }
}
