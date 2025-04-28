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
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['booking_seats_id']);
            $table->dropColumn('booking_seats_id');
    
            // If not already added
            $table->foreignId('booking_id')->after('id')->nullable()->constrained('bookings')->cascadeOnDelete();
      
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
    
            $table->foreignId('booking_seats_id')->nullable()->constrained('booking_seats')->cascadeOnDelete();
       
        });
    }
};
