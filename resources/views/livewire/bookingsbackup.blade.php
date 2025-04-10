<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Book Your Trip')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit.prevent="submitBooking" class="flex flex-col gap-6">
        <!-- Name -->
        <div class="mb-4">
            <label for="trip" class="block text-xl font-medium text-gray-500"><h3>Select a Trip</h3></label>
            <flux:select wire:model.change="currentTripId" searchable  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <flux:select.option value="" class="text-gray-500">-- Choose Trip --</flux:select.option> <!-- Null option -->
          
            @foreach ($trips as $trip)
                    <flux:select.option value="{{ $trip->id }}" class="text-black">
                        {{ ucfirst($trip->routes->start_location) }} to {{ ucfirst($trip->routes->end_location) }}
                        @ {{ date('h:i A', strtotime($trip->departure_time)) }}
                  
                    </flux:select.option>
                @endforeach
            </flux:select>
            <div class="p-2 text-red-500">@error('currentTripId') {{ $message }} @enderror</div>
        </div>

        @if($price)
            <div class="mt-2 text-lg font-semibold dark:text-white">
                Price: GHS {{ $price }}
            </div>
            @else
            <div class="mt-2 text-lg font-semibold dark:text-white">
                Price: N/A
            </div>
        @endif

        <div class="  pb-3">
        <label for="trip" class="block text-xl font-medium text-gray-500 pb-2"><h3>Select Booking Date</h3></label>
        <input type="date" wire:model="tripDate" class="border border-white rounded" />
        <div class="p-2 text-red-500">@error('tripDate') {{ $message }} @enderror</div>
        </div>
        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>