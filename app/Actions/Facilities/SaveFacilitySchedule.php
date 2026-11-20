<?php

namespace App\Actions\Facilities;

use App\Models\Facility;
use App\Models\FacilitySchedule;

/**
 * Sets a facility's opening hours and slot length for one weekday (FR-A3).
 * There is one row per weekday, so saving the same day again updates it.
 */
class SaveFacilitySchedule
{
    /**
     * @param  array{day_of_week: int, open_time: string, close_time: string, slot_minutes: int}  $data
     */
    public function handle(Facility $facility, array $data): FacilitySchedule
    {
        return $facility->schedules()->updateOrCreate(
            ['day_of_week' => (int) $data['day_of_week']],
            [
                'open_time' => $data['open_time'],
                'close_time' => $data['close_time'],
                'slot_minutes' => (int) $data['slot_minutes'],
            ],
        );
    }
}
