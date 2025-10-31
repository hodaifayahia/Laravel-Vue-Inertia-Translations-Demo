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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('chat_channels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('type', ['text', 'file', 'system'])->default('text');
            $table->string('attachment_url')->nullable();
            $table->string('attachment_type')->nullable(); // image, pdf, document
            $table->string('attachment_name')->nullable();
            $table->integer('attachment_size')->nullable(); // bytes
            $table->foreignId('reply_to_message_id')->nullable()->constrained('chat_messages')->onDelete('set null');
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['channel_id', 'created_at']);
            $table->index('user_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
