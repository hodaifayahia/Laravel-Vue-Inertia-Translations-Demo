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
        Schema::create('chat_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('from_role'); // e.g., "Teacher"
            $table->string('to_role'); // e.g., "Parent"
            $table->boolean('can_initiate')->default(true);
            $table->boolean('can_receive')->default(true);
            $table->timestamps();
            
            $table->unique(['from_role', 'to_role']);
            $table->index('from_role');
            $table->index('to_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_permissions');
    }
};
