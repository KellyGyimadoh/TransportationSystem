<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Book Your Trip')" :description="__('Trips cant be booked 30 days after current date')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />


    <!-- Name -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="flex justify-end mb-5">
        <h4 class="p-2">Search Trip</h4> <input class="dark:bg-white rounded text-black" type="text" wire:model.live="search">
        </div>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-800">Route/Trip Location</th>
                    <th scope="col" class="px-6 py-3">Date Booked</th>
                    <th scope="col" class="px-6 py-3">Seats Booked</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-800">Trip Status</th>

                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-800">Price</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-800">Payment Status</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userbookings as $userbooking)
                    <tr wire:key="{{ $userbooking->id }}" class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4">{{ucfirst($userbooking->trip->routes->start_location) }}
                            to {{ ucfirst($userbooking->trip->routes->end_location) }}</td>
                        <td class="px-6 py-4">
                        {{ date('Y-M-d', strtotime($userbooking->trip_date)) }}
                   </td>
                   <td class="px-6 py-4">
                    {{ $userbooking->seatReservations->count() }}
                   </td>
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800"> {{ $userbooking->status }}</td>
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800">GHS {{ $userbooking->trip->price }}</td>
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800"> 
                        @switch($userbooking->payment_status)
                            @case('paid')
                            <button 
                                class="px-3 py-1 bg-green-500 text-lg text-white rounded">
                                Paid
                            </button>
                                @break
                        @case('unpaid')
                        <button 
                                class="px-3 py-1 bg-red-500 text-black text-lg rounded">
                                UnPaid
                            </button>
                        @break
                            @default
                            <button 
                                class="px-3 py-1 bg-blue-500 text-white rounded">
                                N/A
                            </button> 
                        @endswitch
                        </td>


                        <td class="px-6 py-4">
                            @switch( $userbooking->payment_status)
                                @case('paid')
                               <a href="{{ route('tickets',$userbooking->id) }}" wire:navigate> <button 
                                class="px-3 py-1 bg-gray-500 text-white rounded">
                                Print Ticket Receipt
                            </button></a>   
                                    @break
                                @case('unpaid')
                                <a href="{{ route('payment',$userbooking->id) }}" wire:navigate><button 
                                class="px-3 py-1 bg-blue-500 text-white rounded">
                                Make Payment
                            </button></a>
                                @break
                            
                                @default
                                <button 
                                class="px-3 py-1 bg-green-500 text-white rounded">
                                N/A
                            </button>  
                            @endswitch
                            
                        </td>



                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $userbookings->links() }}
    </div>
    <livewire:booking-modal />




    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
       
       <flux:button> <flux:link :href="route('mybookings',Auth::user())" wire:navigate>
       {{ __('View My Bookings') }}</flux:link></flux:button>
    </div>
</div>