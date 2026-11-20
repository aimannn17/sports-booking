<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\SlotStatus;
use Database\Factories\TimeSlotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\CarbonImmutable;

#[Fillable(['facility_id', 'slot_date', 'start_time', 'end_time', 'status'])]
class TimeSlot extends Model
{
    /** @use HasFactory<TimeSlotFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'status' => SlotStatus::class,
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)->whereIn('status', BookingStatus::active());
    }

    #[Scope]
    protected function available(Builder $query): void
    {
        $query->where($query->qualifyColumn('status'), SlotStatus::Available);
    }

    #[Scope]
    protected function forDate(Builder $query, mixed $date): void
    {
        $query->whereDate($query->qualifyColumn('slot_date'), $date);
    }

    /**
     * The moment this slot starts.
     */
    public function startsAt(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->slot_date->format('Y-m-d').' '.$this->start_time);
    }

    public function hasStarted(): bool
    {
        return $this->startsAt()->isPast();
    }

    public function timeRange(): string
    {
        return substr($this->start_time, 0, 5).' – '.substr($this->end_time, 0, 5);
    }
}
