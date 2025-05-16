@php
    $baseImageUrl = asset('images');
    
@endphp

<div class="p-2">
    <!-- Image Slide with Fade Transition -->
   
        <div
          
            class=" aspect-video overflow-hidden   bg-cover bg-center rounded-xl border border-neutral-200 dark:border-neutral-700"
            style="background-image: url('{{ asset('images/'.$images[$counter]) }}');">
    
    <!-- Buttons -->
   
    <!-- Slide Indicators (dots) -->
    <div class="absolute  bottom-10  left-1/2   flex transform -translate-x-1/8 space-x-2 z-20 ">
            @foreach ($images as $index=>$image )
            @php
               $color=null;
               if($counter!==$index){
               $color='bg-white';
               }else if($counter === $index){
                $color='bg-blue-500';
               }
               @endphp
            <button
               class="w-3 h-3 rounded-full bg-red-300 {{ $color }}"
              
           ></button>  
            @endforeach
           
      
    </div>
    
    </div>
    <div class=" flex justify-evenly">
    <div>
        <button 
           wire:click="previousPicture" 
            class="bg-red-300 p-3 rounded"
        >
            Previous
        </button>
    </div>
        <div>
            <h1 class="text-center text-lg font-bold">
                Image <span>{{ $counter+1 }}</span> of <span>{{ count($images) }}</span>
            </h1>
        </div>
        <div>
        <button 
        wire:click="nextPicture"  
            class="bg-green-400 p-3 rounded"
        >
            Next
        </button>
        </div>
    </div>

    </div> 
    
        
   
   
