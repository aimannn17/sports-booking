<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'time_slot_id', 'amount_due', 'status', 'payment_deadline', 'cancel_reason'])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount_due' => 'decimal:2',
            'status' => BookingStatus::class,
            'payment_deadline' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereIn($query->qualifyColumn('status'), BookingStatus::active());
    }

    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->whereHas('timeSlot', fn (Builder $q) => $q->whereDate('slot_date', '>=', today()));
    }

    #[Scope]
    protected function past(Builder $query): void
    {
        $query->whereHas('timeSlot', fn (Builder $q) => $q->whereDate('slot_date', '<', today()));
    }

    public function isActive(): bool
    {
        return in_array($this->status->value, BookingStatus::active(), true);
    }

    public function needsPayment(): bool
    {
        return (float) $this->amount_due > 0;
    }

    public function isPaid(): bool
    {
        return $this->payment?->status === PaymentStatus::Paid;
    }
}
