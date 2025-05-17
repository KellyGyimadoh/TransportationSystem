<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Make Payment')" :description="__('Payment Made Are Non-Refundable')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="makePayment" class="flex flex-col gap-6">
        <!-- Name -->
       <div>
       <h3>Route: {{ $bookings->trip->routes->start_location }} To {{ $bookings->trip->routes->end_location }}</h3>

       </div>
       <div>
        <h3>Booking Date: {{ $bookings->trip_date }}</h3>
       </div>

       <div>
        <h3>Amount For {{ $bookings->seatReservations->count() }} Seats Ordered: 
        GHS {{ $amount }}</h3>
       </div>
<div>
    <h3>Payment Status: {{ $bookings->payment_status }}</h3>
</div>

 

<!-- <flux:radio.group wire:model="paymentMethod" label="Select your payment method">
    <flux:radio value="card" label="Credit Card" checked />
    <flux:radio value="cash" label="Cash" />
    <flux:radio value="mobile_money" label="Mobile Money" />
</flux:radio.group> -->


        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __("Pay $amount") }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Pay Later') }}
        <flux:link :href="route('mybookings')" wire:navigate>{{ __('View My Bookings') }}</flux:link>
    </div>
</div>
@script
<script>
    window.addEventListener('initiatePaystack', function (e) {
        const data=e.detail;
       
        let handler = PaystackPop.setup({
            key: '{{ config('services.paystack.key') }}',
            email: data.email,
            amount: data.amount,
            ref: data.reference,
            currency:'GHS',
            callback: function(response) {
                // Payment success
                Livewire.dispatch('paymentSuccessful',{response: response});
            },
            onClose: function() {
                alert('Transaction was not completed.');
            }
        });
       handler.openIframe();
    });
</script>
@endscript