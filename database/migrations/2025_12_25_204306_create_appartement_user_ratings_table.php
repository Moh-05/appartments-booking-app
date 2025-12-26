<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('appartement_user_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('appartement_id')->constrained()->onDelete('cascade');


            $table->decimal('rating', 2, 1); 

            $table->timestamps();

    
            $table->unique(['user_id', 'appartement_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('appartement_user_ratings');
    }
};
