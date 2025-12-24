<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
    
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending','booked','cancelled','completed') DEFAULT 'pending'");
    }

    public function down(): void
    {
    
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending','booked','cancelled') DEFAULT 'pending'");
    }
};