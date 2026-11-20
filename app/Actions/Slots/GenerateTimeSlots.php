<?php

namespace App\Actions\Slots;

use App\Enums\SlotStatus;
use App\Models\Facility;
use App\Models\TimeSlot;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

/**
 * Creates slots for a date range from a facility's weekly schedule (FR-A4).
 * Existing slots are left alone, so running it twice is safe.
 */
class GenerateTimeSlots
{
    /**
     * @return int The number of new slots created.
     */
    public function handle(Facility $facility, CarbonInterface|string $from, CarbonInterface|string $to): int
    {
        $schedules = $facility->schedules()->get()->keyBy('day_of_week');
        $from = Carbon::parse($from)->startOfDay()->max(today()->toMutable());
        $to = Carbon::parse($to)->startOfDay();
        $created = 0;

        foreach (CarbonPeriod::create($from, $to) as $date) {
            $schedule = $schedules->get($date->dayOfWeek);

            if (! $schedule) {
                continue; // Closed that weekday.
            }

            $start = Carbon::parse($date->format('Y-m-d').' '.$schedule->open_time);
            $close = Carbon::parse($date->format('Y-m-d').' '.$schedule->close_time);

            while ($start->copy()->addMinutes($schedule->slot_minutes)->lessThanOrEqualTo($close)) {
                $end = $start->copy()->addMinutes($schedule->slot_minutes);

                $exists = TimeSlot::query()
                    ->where('facility_id', $facility->id)
                    ->whereDate('slot_date', $date)
                    ->where('start_time', $start->format('H:i:s'))
                    ->exists();

                if (! $exists) {
                    TimeSlot::create([
                        'facility_id' => $facility->id,
                        'slot_date' => $date->format('Y-m-d'),
                        'start_time' => $start->format('H:i:s'),
                        'end_time' => $end->format('H:i:s'),
                        'status' => SlotStatus::Available,
                    ]);
                    $created++;
                }

                $start = $end;
            }
        }

        return $created;
    }
}
