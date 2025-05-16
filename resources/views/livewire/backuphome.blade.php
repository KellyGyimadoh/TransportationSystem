@php
    $baseImageUrl = asset('images');
@endphp

<div 
    x-data="{
        counter: @entangle('counter').defer,
        total: {{ count($images) }},
        startAutoplay() {
            setInterval(() => {
                this.counter = (this.counter + 1) % this.total;
            }, 5000);
        }
    }"
    x-init="startAutoplay"
    class="relative"
>
    <!-- Image Slide with Fade Transition -->
    <template x-for="(image, index) in {{ Js::from($images) }}" :key="index">
        <div
            x-show="counter === index"
            x-transition:enter="transition-opacity duration-1000"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-1000"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-cover bg-center rounded-xl border border-neutral-200 dark:border-neutral-700"
            :style="'background-image: url(' + @js($baseImageUrl) + '/' + image + ')' "


        ></div>
    </template>

    <!-- Buttons -->
    <div class="relative z-10 flex justify-evenly mt-4">
        <button 
            @click="counter = (counter - 1 + total) % total" 
            class="bg-red-300 p-3 rounded"
        >
            Previous
        </button>

        <div>
            <h1 class="text-center text-lg font-bold">
                Image <span x-text="counter + 1"></span> of <span x-text="total"></span>
            </h1>
        </div>

        <button 
            @click="counter = (counter + 1) % total" 
            class="bg-green-400 p-3 rounded"
        >
            Next
        </button>
    </div>

    <!-- Slide Indicators (dots) -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
        <template x-for="(image, index) in total" :key="index">
            <button
                @click="counter = index"
                class="w-3 h-3 rounded-full"
                :class="{
                    'bg-white': counter !== index,
                    'bg-blue-500': counter === index
                }"
            ></button>
        </template>
    </div>
</div>
