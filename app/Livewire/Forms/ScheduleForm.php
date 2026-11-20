<?php

namespace App\Livewire\Forms;

use App\Models\FacilitySchedule;
use Livewire\Form;

class ScheduleForm extends Form
{
    public ?int $day_of_week = 1;

    public string $open_time = '08:00';

    public string $close_time = '22:00';

    public int $slot_minutes = 60;

    protected function rules(): array
    {
        return [
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'open_time' => ['required', 'date_format:H:i'],
            'close_time' => ['required', 'date_format:H:i', 'after:open_time'],
            'slot_minutes' => ['required', 'integer', 'in:30,60,90,120'],
        ];
    }

    protected function messages(): array
    {
        return ['close_time.after' => 'The closing time must be after the opening time.'];
    }

    public function setSchedule(FacilitySchedule $schedule): void
    {
        $this->day_of_week = $schedule->day_of_week;
        $this->open_time = substr($schedule->open_time, 0, 5);
        $this->close_time = substr($schedule->close_time, 0, 5);
        $this->slot_minutes = $schedule->slot_minutes;
    }
}
