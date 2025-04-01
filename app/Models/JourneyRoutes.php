<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourneyRoutes extends Model
{
    /** @use HasFactory<\Database\Factories\JourneyRoutesFactory> */
    use HasFactory;
    protected $fillable = ['start_location', 'end_location', 'distance', 'estimated_time'];

    public function trips(){
        return $this->hasMany(Trips::class);
    }
}
