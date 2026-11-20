<?php

namespace Database\Seeders;

use App\Actions\Slots\GenerateTimeSlots;
use App\Enums\SlotStatus;
use App\Models\Facility;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

/**
 * Slots for today and the next 7 days, built with the same Action the admin uses,
 * plus a few past slots so the "Past" tab has history.
 */
class TimeSlotSeeder extends Seeder
{
    public function run(GenerateTimeSlots $generate): void
    {
        foreach (Facility::active()->get() as $facility) {
            $generate->handle($facility, today(), today()->addDays(7));

            foreach ([7, 3] as $daysAgo) {
                foreach (['18:00:00', '19:00:00'] as $start) {
                    TimeSlot::create([
                        'facility_id' => $facility->id,
                        'slot_date' => today()->subDays($daysAgo),
                        'start_time' => $start,
                        'end_time' => substr_replace($start, (string) ((int) substr($start, 0, 2) + 1), 0, 2),
                        'status' => SlotStatus::Available,
                    ]);
                }
            }
        }

        // One blocked slot to show the red state.
        TimeSlot::query()
            ->where('status', SlotStatus::Available)
            ->whereDate('slot_date', today()->addDays(2))
            ->where('start_time', '10:00:00')
            ->first()
            ?->update(['status' => SlotStatus::Blocked]);
    }
}
