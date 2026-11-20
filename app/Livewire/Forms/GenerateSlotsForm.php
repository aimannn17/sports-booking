<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class GenerateSlotsForm extends Form
{
    public ?int $facility_id = null;

    public string $from = '';

    public string $to = '';

    protected function rules(): array
    {
        return [
            'facility_id' => ['required', 'integer', 'exists:facilities,id'],
            'from' => ['required', 'date', 'after_or_equal:today'],
            'to' => ['required', 'date', 'after_or_equal:from', 'before_or_equal:'.now()->addDays(60)->toDateString()],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['facility_id' => 'facility', 'from' => 'start date', 'to' => 'end date'];
    }

    protected function messages(): array
    {
        return ['to.before_or_equal' => 'Generate at most 60 days ahead at a time.'];
    }
}
