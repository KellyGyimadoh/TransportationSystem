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
        Schema::create('seat_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seat_id')->constrained('seats')->onDelete('cascade'); // Refers to Seats table
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade'); // Refers to Trips table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Refers to Users
            $table->date('booking_date');
            $table->enum('status', ['reserved', 'cancelled', 'completed'])->default('reserved');
            $table->timestamps();
            $table->unique(['seat_id', 'trip_id', 'booking_date'], 'unique_seat_trip_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_reservations');
    }
};
