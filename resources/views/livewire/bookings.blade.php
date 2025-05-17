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
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-800">Route</th>
                    <th scope="col" class="px-6 py-3">Departure Time</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-800">Price</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trips as $trip)
                    <tr wire:key="{{ $trip->id }}" class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4">{{ucfirst($trip->routes->start_location) }}
                            to {{ ucfirst($trip->routes->end_location) }}</td>
                        <td class="px-6 py-4">
                        {{ date('h:i A', strtotime($trip->departure_time)) }}</td>
                        <td class="px-6 py-4">
                           
                            <span class="border @switch($trip->status)
                                @case($trip->status=='scheduled')
                                    bg-yellow-300
                                    @break
                                 @case($trip->status=='ongoing')
                                        bg-blue-300 
                                    @break
                                    @case($trip->status=='completed')
                                        bg-green-300 
                                    @break
                                      @case($trip->status=='canceled')
                                        bg-red-300 
                                    @break
                            
                                @default
                                    
                            @endswitch border-green-500 rounded  px-5 text-black text-sm">
                        {{ ucfirst($trip->status) }}</span></td>
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800">GHS {{ $trip->price }}</td>


                        <td class="px-6 py-4">
                            @switch($trip->status)
                                @case('scheduled')
                                <button wire:click="$dispatch('openModal', { tripId: {{ $trip->id }} })"
                                class="px-3 py-1 bg-blue-500 text-white rounded">
                                Confirm Booking
                            </button>
                                    @break

                                @case('ongoing')
                                <button wire:click="$dispatch('openModal', { tripId: {{ $trip->id }} })"
                                class="px-3 py-1 bg-blue-500 text-white rounded">
                                Book New Date
                            </button> 
                                @break
                            
                                @default
                                <button wire:click="$dispatch('openModal', { tripId: {{ $trip->id }} })"
                                class="px-3 py-1 bg-blue-500 text-white rounded">
                                Confirm Booking
                            </button>
                            @endswitch
                            
                        </td>
                       


                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $trips->links() }}
    </div>
    <livewire:booking-modal />




    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400 mb-3">
       
       <flux:button> <flux:link :href="route('mybookings')" wire:navigate>
       {{ __('View My Bookings') }}</flux:link></flux:button>
    </div>
</div>