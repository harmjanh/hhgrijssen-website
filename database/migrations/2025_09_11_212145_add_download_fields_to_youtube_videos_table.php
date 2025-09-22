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
        Schema::table('youtube_videos', function (Blueprint $table) {
            $table->string('download_status')->nullable()->after('is_published');
            $table->string('video_file_path')->nullable()->after('download_status');
            $table->string('audio_file_path')->nullable()->after('video_file_path');
            $table->timestamp('downloaded_at')->nullable()->after('audio_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('youtube_videos', function (Blueprint $table) {
            $table->dropColumn(['download_status', 'video_file_path', 'audio_file_path', 'downloaded_at']);
        });
    }
};
