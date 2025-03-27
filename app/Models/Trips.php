<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trips extends Model
{
    /** @use HasFactory<\Database\Factories\TripsFactory> */
    use HasFactory;
    protected $fillable = ['bus_id', 'route_id', 'departure_time', 'arrival_time', 'price', 'status'];

     // A trip has many bookings
    public function bookings(){
        return $this->hasMany(Bookings::class);
    }

    // A trip belongs to a bus
    public function bus(){
        return $this->hasMany(Buses::class);
    }

     // A trip has a route
    public function routes(){
            return $this->belongsTo(JourneyRoutes::class,'route_id');
        }
}
