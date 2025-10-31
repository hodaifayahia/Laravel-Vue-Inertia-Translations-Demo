<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table stores which specific users are assigned to handle 
     * communications from a particular role.
     * 
     * Example: Role "doctor" wants to talk to "admin" role
     * -> Assign Admin User 1 and Admin User 2 to handle doctor requests
     * -> ALL doctors will see only these 2 admins
     */
    public function up(): void
    {
        Schema::create('chat_role_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('from_role'); // e.g., "doctor" - the role that initiates chat
            $table->string('to_role'); // e.g., "admin" - the role they want to talk to
            $table->foreignId('assigned_user_id')->constrained('users')->onDelete('cascade'); // Specific user assigned to handle this role's requests
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null'); // Who made this assignment
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['from_role', 'to_role']);
            $table->index('assigned_user_id');
            
            // Prevent duplicate assignments
            $table->unique(['from_role', 'to_role', 'assigned_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_role_assignments');
    }
};
