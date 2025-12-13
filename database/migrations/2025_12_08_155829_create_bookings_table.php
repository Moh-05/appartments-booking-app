<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
    $table->id();

    // Relations
    $table->foreignId('appartement_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');

    // Booking details
    $table->date('start_date')->nullable(); // set later by admin
    $table->date('end_date');               // chosen by user
    $table->enum('status', [
        'pending',
        'booked',
        'cancelled'
    ])->default('pending');

    // Optional extras
    $table->decimal('total_price', 10, 2)->nullable();
    $table->text('notes')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
