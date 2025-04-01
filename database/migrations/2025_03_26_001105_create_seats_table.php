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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade'); // Links to buses
            $table->string('seat_number'); // Example: '1A', '2B'
            $table->enum('status', ['available', 'booked', 'reserved'])->default('available');
       
            $table->timestamps();
            $table->unique(['bus_id', 'seat_number']); // Ensures no duplicate seat numbers per bus

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
