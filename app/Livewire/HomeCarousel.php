<?php

namespace App\Livewire;

use Livewire\Component;

class HomeCarousel extends Component
{
    public $images=['BRT.png','bg2.jpg','background1.png'];
    public int $counter=0;

    public int $total;

    protected $listeners=['autoNext'=>'nextPicture'];

    public function previousPicture(){
        // if($this->counter===0){
        //     return;
        // }
        // $this->counter--;
       
        //$this->counter=max(0,$this->counter-1);
        $this->counter = ($this->counter - 1 + count($this->images)) % count($this->images);
        
    }
    public function nextPicture(){
        // if($this->counter===count($this->images)-1){
        //     return;
        // }
        // $this->counter++;
        //$this->counter=min(count($this->images)-1,$this->counter+1);
        $this->counter = ($this->counter + 1) % count($this->images);    
    }
    public function render()
    {  
        return view('livewire.home-carousel') 
        ->withLivewire(fn ($livewire) => $livewire->on('autoNext', fn () => $this->nextPicture()));

        
    }
    
   
       
}
