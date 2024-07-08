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
        Schema::create('clinic_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slot_id');
            $table->string('user_phone_no'); // storing user phone number
            $table->string('reference_no')->unique();
            $table->date('booking_date');
            $table->boolean('is_confirmed')->default(false);
            $table->timestamps();

            // foreign key constraint
            $table->foreign('slot_id')->references('id')->on('clinic_timeslots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_bookings');
    }
};
