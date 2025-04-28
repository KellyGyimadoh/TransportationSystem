<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Make Payment')" :description="__('Payment Made Are Non-Refundable')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

  <h1>Tickets page</h1>
  <div>
       <h3>Route: {{ $bookings->trip->routes->start_location }} To {{ $bookings->trip->routes->end_location }}</h3>

       </div>
       <div>
        <h3>Booking Date: {{ $bookings->trip_date }}</h3>
       </div>

       <div>
        <h3>Amount Paid: GHS {{  $paymentDetails->amount  }}</h3>
       </div>
       <div>
        <h3>Payment Method: {{  $paymentDetails->payment_method  }}</h3>
       </div>
       <p>Passenger: {{ $bookings->user->name }}</p>

<p>Trip Date: {{ $bookings->trip_date }}</p>

@foreach($bookings->seatReservations as $seat)
    @php
        $ticket = $seat->ticket; // assuming a relationship
    @endphp
    @if($ticket)
        <div class="border p-4 my-2">
            <p><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
            <p><strong>Status:</strong> {{ $ticket->status }}</p>
            {!! QrCode::size(150)->generate($ticket->ticket_number) !!}

        </div>
    @endif
@endforeach

<!-- Optional print button -->
<button onclick="window.print()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Print Ticket</button>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Pay Later') }}
        <flux:link :href="route('mybookings')" wire:navigate>{{ __('View My Bookings') }}</flux:link>
    </div>
</div>
