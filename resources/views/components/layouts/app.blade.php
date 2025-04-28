<x-layouts.app.header :title="$title ?? null">

    <flux:main>
       
    @if (session('success'))
   
    <div class="bg-green-300 border border-green-400 text-green-900 px-4 py-3 rounded
     relative mb-4 alertbox  transition:opacity 0.4s ease-in-out " role="alert">
        <strong class="font-bold">Success:</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif
@if (session('error'))
   
   <div class="bg-red-300 border border-red-400 text-black-900 px-4 py-3 rounded
    relative mb-4 alertbox  transition:opacity 0.4s ease-in-out" role="alert">
       <strong class="font-bold">Error:</strong>
       <span class="block sm:inline">{{ session('error') }}</span>
   </div>
@endif
    
        {{ $slot }}
    </flux:main>
</x-layouts.app.header>
