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
        Schema::create('chat_channel_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('chat_channels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['member', 'admin'])->default('member');
            $table->boolean('is_blocked')->default(false);
            $table->foreignId('blocked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('block_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            
            $table->unique(['channel_id', 'user_id']);
            $table->index('user_id');
            $table->index('is_blocked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_channel_users');
    }
};
