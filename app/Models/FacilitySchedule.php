<?php

namespace App\Models;

use Database\Factories\FacilityScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['facility_id', 'day_of_week', 'open_time', 'close_time', 'slot_minutes'])]
class FacilitySchedule extends Model
{
    /** @use HasFactory<FacilityScheduleFactory> */
    use HasFactory;

    public const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'slot_minutes' => 'integer',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function dayName(): string
    {
        return self::DAYS[$this->day_of_week];
    }
}
