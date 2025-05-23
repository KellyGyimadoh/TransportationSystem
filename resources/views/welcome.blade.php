<x-layouts.app title="Welcome {{Auth::user()->name ?? null }}">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border
             border-neutral-200 dark:border-neutral-700 bg-white items-center ">
               <h1 class="text-center m-3 p-3 dark:text-black text-3xl">
               <a href="{{ route('bookings') }}">BOOK A TRIP</a>   
               </h1>
              
            </div>
            @foreach ($trips as $trip )
            <div wire:key="{{ $trip->id }}" class="relative aspect-video bg-white overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <div class="flex flex-col justify-evenly items-center text-black border dark:border-red-600 m-5 ">
                        <div class=" p-2  border border-yellow-200">
                            <h3>{{ucwords($trip->routes->start_location) }} To {{ucwords($trip->routes->end_location) }}</h3>
                        </div>
                        <div>
                         Departure Time @ {{ date('H:i A',strtotime($trip->departure_time)) }}
                        </div>
                        <div class="p-3">
                            @switch($trip->status)
                                @case('scheduled')
                                <button wire:click="$dispatch('openModal', { tripId: {{ $trip->id }} })"
                                class="px-3 py-1 bg-green-500 text-white rounded">
                                Confirm Booking
                            </button>
                                    @break

                                @case('ongoing')
                                <button wire:click="$dispatch('openModal', { tripId: {{ $trip->id }} })"
                                class="px-3 py-1 bg-blue-500 text-white rounded">
                                Book New Date
                            </button> 
                            
                                    @break

                                @case('canceled')
                                <button 
                                class="px-3 py-1 bg-red-500 text-white rounded">
                                Unavailable
                            </button> 
                                @break
                            
                                @default
                                <button wire:click="$dispatch('openModal', { tripId: {{ $trip->id }} })"
                                class="px-3 py-1 bg-blue-500 text-white rounded">
                                Confirm Booking
                            </button>
                            @endswitch
                        </div>
                    </div>
                </div>   
            @endforeach
           
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />bi
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />hi
        </div>
       
    </div>
    <livewire:booking-modal /> 
</x-layouts.app>
