<?php

use App\Actions\Payments\RecordPayment;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Livewire\Forms\PaymentForm;
use App\Models\Booking;
use App\Models\Payment;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/*
 * FR-A6 / FR-A7: external bookings, unpaid first, with a record-payment modal.
 */
new #[Title('Payments')] class extends Component {
    use WithPagination;

    #[Url]
    public string $filter = 'unpaid';

    #[Url(except: '')]
    public string $search = '';

    public PaymentForm $paymentForm;

    #[Locked]
    public ?int $payingBookingId = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Payment::class);

        if (! in_array($this->filter, ['unpaid', 'paid', 'all'], true)) {
            $this->filter = 'unpaid';
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['filter', 'search'], true)) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function bookings()
    {
        $paid = fn ($q) => $q->where('status', PaymentStatus::Paid);

        return Booking::query()
            ->where('amount_due', '>', 0)
            ->with(['user', 'timeSlot.facility', 'payment'])
            ->when($this->filter === 'unpaid', fn ($q) => $q
                ->where('status', '!=', BookingStatus::Cancelled->value)
                ->whereDoesntHave('payment', $paid))
            ->when($this->filter === 'paid', fn ($q) => $q->whereHas('payment', $paid))
            ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('booking_code', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('payment', fn ($p) => $p->where('receipt_no', 'like', "%{$this->search}%"))))
            ->orderBy($this->filter === 'unpaid' ? 'payment_deadline' : 'updated_at', $this->filter === 'unpaid' ? 'asc' : 'desc')
            ->paginate(15);
    }

    #[Computed]
    public function totals(): array
    {
        return [
            'unpaid' => (float) Booking::where('amount_due', '>', 0)
                ->where('status', '!=', BookingStatus::Cancelled->value)
                ->whereDoesntHave('payment', fn ($q) => $q->where('status', PaymentStatus::Paid))
                ->sum('amount_due'),
            'paid' => (float) Payment::where('status', PaymentStatus::Paid)->sum('amount'),
        ];
    }

    public function startPayment(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);
        $this->authorize('record', [Payment::class, $booking]);

        $this->payingBookingId = $booking->id;
        $this->resetValidation();
        $this->paymentForm->forBooking($booking);
        Flux::modal('record-payment')->show();
    }

    public function recordPayment(RecordPayment $action): void
    {
        $booking = Booking::findOrFail($this->payingBookingId);
        $this->authorize('record', [Payment::class, $booking]);
        $data = $this->paymentForm->validate();

        try {
            $payment = $action->handle($booking, auth()->user(), $data);
        } catch (ValidationException $e) {
            $this->addError('paymentForm.amount', $e->validator->errors()->first());

            return;
        }

        $this->payingBookingId = null;
        Flux::modal('record-payment')->close();
        Flux::toast(variant: 'success', text: __(':code paid. Receipt :no.', ['code' => $booking->booking_code, 'no' => $payment->receipt_no]));
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Payments') }}</flux:heading>
            <flux:subheading>{{ __('External bookings. Record cash or bank transfer payments here.') }}</flux:subheading>
        </div>
        <div class="flex gap-6 text-sm">
            <div><flux:text>{{ __('Outstanding') }}</flux:text><div class="text-lg font-semibold text-amber-600">RM{{ number_format($this->totals['unpaid'], 2) }}</div></div>
            <div><flux:text>{{ __('Collected') }}</flux:text><div class="text-lg font-semibold">RM{{ number_format($this->totals['paid'], 2) }}</div></div>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <flux:radio.group wire:model.live="filter" variant="segmented">
            <flux:radio value="unpaid" :label="__('Unpaid')" />
            <flux:radio value="paid" :label="__('Paid')" />
            <flux:radio value="all" :label="__('All')" />
        </flux:radio.group>
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Code, name or receipt')" class="sm:max-w-xs" />
    </div>

    <flux:table :paginate="$this->bookings">
        <flux:table.columns>
            <flux:table.column>{{ __('Booking') }}</flux:table.column>
            <flux:table.column>{{ __('User') }}</flux:table.column>
            <flux:table.column>{{ __('Facility & slot') }}</flux:table.column>
            <flux:table.column>{{ __('Amount') }}</flux:table.column>
            <flux:table.column>{{ __('Deadline / receipt') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->bookings as $booking)
                <flux:table.row :key="$booking->id">
                    <flux:table.cell>
                        <flux:link :href="route('admin.bookings.show', $booking)" wire:navigate class="font-mono text-sm">{{ $booking->booking_code }}</flux:link>
                        <div><flux:badge size="sm" :color="$booking->status->color()">{{ $booking->status->label() }}</flux:badge></div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $booking->user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $booking->timeSlot->facility->name }}<div class="text-xs text-zinc-500">{{ $booking->timeSlot->slot_date->format('j M') }}, {{ $booking->timeSlot->timeRange() }}</div></flux:table.cell>
                    <flux:table.cell>RM{{ number_format($booking->amount_due, 2) }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($booking->isPaid())
                            <flux:badge size="sm" color="green">{{ __('Paid') }}</flux:badge>
                            <flux:link :href="route('payments.receipt', $booking->payment)" wire:navigate class="ms-1 font-mono text-sm">{{ $booking->payment->receipt_no }}</flux:link>
                        @elseif ($booking->payment_deadline)
                            <span @class(['text-red-600' => $booking->payment_deadline->isPast()])>{{ $booking->payment_deadline->format('j M, H:i') }}</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        @if (! $booking->isPaid() && $booking->status !== \App\Enums\BookingStatus::Cancelled)
                            <flux:button size="sm" variant="primary" icon="banknotes" wire:click="startPayment({{ $booking->id }})" data-test="record-payment-{{ $booking->id }}">{{ __('Record payment') }}</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="6">{{ $filter === 'unpaid' ? __('Nothing outstanding. Every external booking is paid.') : __('No payments found.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @include('partials.record-payment-modal')
</div>
