
<div >
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl"  >
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border
             border-neutral-200 dark:border-neutral-700 items-center "
              style="background-image: url('{{ asset('images/BRT.png') }}');">
             @if(!empty($trips))
             <ul class=" text-center dark:text-black text-xl ">

                @foreach ($trips as $trip )
                    <li class="p-2"> <h4>{{ucwords($trip->routes->start_location) }}
                         To {{ucwords($trip->routes->end_location) }}</h4>
                    </li>
                    
                    @endforeach
                </ul>
                @else
                <h1 class="text-center m-3 p-3 dark:text-black text-2xl">No Trips Available</h1>
                @endif
               <h1 class="text-center m-3 p-3 dark:text-black text-2xl">
                 
               <a href="{{ route('bookings') }}"><button  class="px-3 py-1 bg-green-500 text-white rounded cursor-pointer">
                BOOK A TRIP</button></a>   
               </h1>
              
            </div>
            @if(!empty($trips))
            @foreach ($trips as $trip )
            <div wire:key="{{ $trip->id }}" class="relative aspect-video
             overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-cover bg-left-top" 
             style="background-image: url('{{ asset('images/background1.png') }}');">
                    <div class="flex flex-col justify-evenly items-center text-black border rounded-3xl shadow-2xl dark:border-red-600 m-5 ">
                        <div class=" p-2 border-b-2 border-yellow-500 rounded-b-2xl mb-2">
                            <h3 class="text-3xl text-indigo-700 ">{{ucwords($trip->routes->start_location) }} 
                                To {{ucwords($trip->routes->end_location) }}</h3>
                        </div>
                        <div class="text-2xl font-bold text-red-500 ">
                         Departure Time @ {{ date('H:i A',strtotime($trip->departure_time)) }}
                        </div>
                        @guest
                        <div class="p-5 m-2">
                            @switch($trip->status)
                                @case('scheduled')
                                <button wire:click="alertMessage"
                                class="px-3 py-1 bg-green-500 text-white rounded">
                                Confirm Booking
                            </button>
                                    @break

                                @case('ongoing')
                                <button wire:click="alertMessage"
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
                                <button wire:click="alertMessage"
                                class="px-3 py-1 bg-blue-500 text-white rounded">
                                Confirm Booking
                            </button>
                            @endswitch
                        </div>  
                       @else
                        <div class="p-5 m-2">
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
                        @endguest
                    </div>
                </div>   
            @endforeach
           @endif
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />bi
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />hi
        </div>
       
    </div>
    
    <livewire:booking-modal /> 
    
   

</div>  