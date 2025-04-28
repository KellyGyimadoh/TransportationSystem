<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seat_reservations', function (Blueprint $table) {
            // $table->dropForeign(['trip_id']);
            // $table->dropForeign(['seat_id']);
            
            $table->dropUnique('unique_seat_trip_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seat_reservations', function (Blueprint $table) {
            $table->unique(['seat_id', 'trip_id', 'booking_date'], 'unique_seat_trip_date');
    
        });
    }
};
