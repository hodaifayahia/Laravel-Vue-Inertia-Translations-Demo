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
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialization_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->integer('years_experience')->default(0);
            $table->integer('slot_duration')->default(30); // in minutes
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Ensure one profile per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
