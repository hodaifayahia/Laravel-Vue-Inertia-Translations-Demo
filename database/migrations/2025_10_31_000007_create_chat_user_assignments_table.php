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
        Schema::create('chat_user_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('assignable_role'); // e.g., "Teacher"
            $table->foreignId('assigned_user_id')->constrained('users')->onDelete('cascade'); // Specific Admin ID
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade'); // Super Admin ID
            $table->timestamps();
            
            $table->index('assignable_role');
            $table->index('assigned_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_user_assignments');
    }
};
