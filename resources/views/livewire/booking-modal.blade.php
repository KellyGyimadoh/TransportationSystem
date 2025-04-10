<div class="@if(!$show) hidden @endif fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
    <div class="bg-gray-500 p-6 rounded shadow-lg w-full max-w-md text-black">
        <h2 class="text-xl font-bold mb-4">Confirm Your Booking</h2>

        <p class="inline-block"><strong class="text-xl">Trip:</strong> {{ $tripDetails }}</p>

        <div class="mb-4">
            <label for="tripDate" class="text-xl p-3 inline-block">Select Date:</label>
            <input type="date" wire:model="tripDate" class="mt-1 block w-full border border-black rounded px-2 py-1">
            @error('tripDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end gap-2">
            <button wire:click="submitBooking" class="bg-blue-500 text-white px-4 py-2 rounded">Book</button>
            <button wire:click="closeModal" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
        </div>
    </div>
</div>
