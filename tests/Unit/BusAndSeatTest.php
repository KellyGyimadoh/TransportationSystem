<?php

use App\Models\Buses;
use App\Models\Seats;


test('buses have many seats', function () {
    $buses=Buses::pluck('id',)->toArray();
    $seats=Seats::all();
    foreach($seats as $seat){
    expect($buses)->toContain($seat->bus_id); 
    }
   
});
