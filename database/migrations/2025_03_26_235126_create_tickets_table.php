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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_seats_id')->constrained('booking_seats')->cascadeOnDelete();
            $table->string('ticket_number');
            $table->string('qr_code')->nullable();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('issued_for')->constrained('users')->cascadeOnDelete();
            $table->timestamp('issue_date')->default(now());
            $table->enum('status', ['valid','used','expired'])->default('valid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
