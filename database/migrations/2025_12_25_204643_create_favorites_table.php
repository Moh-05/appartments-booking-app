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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            // علاقة مع المستخدم
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // علاقة مع الشقة
            $table->foreignId('appartement_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();

            // تأكد ما يتكرر نفس المستخدم مع نفس الشقة
            $table->unique(['user_id', 'appartement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};

