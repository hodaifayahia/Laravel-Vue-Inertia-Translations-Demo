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
        Schema::table('chat_messages', function (Blueprint $table) {
            // Add attachment_path column (some code uses this instead of attachment_url)
            if (!Schema::hasColumn('chat_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
        });
    }
};
