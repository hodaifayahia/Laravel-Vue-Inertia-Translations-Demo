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
        Schema::create('provider_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained()->onDelete('cascade');
            $table->integer('day_of_week'); // 0 = Sunday, 6 = Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Ensure unique schedule per day per provider
            $table->unique(['provider_profile_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_schedules');
    }
};
