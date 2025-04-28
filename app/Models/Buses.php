<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buses extends Model
{
    /** @use HasFactory<\Database\Factories\BusesFactory> */
    use HasFactory;
    protected $fillable = ['plate_number', 'model', 'capacity', 'driver_id', 'status'];
    public function driver(){
        return $this->belongsTo(User::class,'driver_id');
    }
    //has many trips
    public function trips(){
        return $this->hasMany(Trips::class);
    }
    // A bus has many seats
    public function seats()
    {
        return $this->hasMany(Seats::class,'bus_id');
    }

}
