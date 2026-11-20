{{--
    Record-payment form (FR-A6), shared by the admin booking page and the payments list.
    The parent component needs a PaymentForm named $paymentForm and a recordPayment() action.
    Alpine reads the chosen method straight from Livewire ($wire) and shows the
    reference field only for bank transfers, without a round trip.
--}}
<flux:modal name="record-payment" class="md:w-96">
    <form wire:submit="recordPayment" class="flex flex-col gap-5">
        <div>
            <flux:heading size="lg">{{ __('Record payment') }}</flux:heading>
            <flux:text class="mt-1">{{ __('A receipt number is generated when you save.') }}</flux:text>
        </div>

        <flux:input wire:model="paymentForm.amount" type="number" step="0.01" min="0.01" :label="__('Amount (RM)')" required />

        <flux:radio.group wire:model="paymentForm.method" :label="__('Method')">
            <flux:radio value="cash" :label="__('Cash')" />
            <flux:radio value="bank_transfer" :label="__('Bank transfer')" />
        </flux:radio.group>

        <div x-show="$wire.paymentForm.method === 'bank_transfer'" x-cloak x-transition>
            <flux:input wire:model="paymentForm.reference_no" :label="__('Bank reference no.')" placeholder="e.g. MBB12345678" />
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
            <flux:button type="submit" variant="primary" data-test="save-payment">{{ __('Mark as paid') }}</flux:button>
        </div>
    </form>
</flux:modal>
