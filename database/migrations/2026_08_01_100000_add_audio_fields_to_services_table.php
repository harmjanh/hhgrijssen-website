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
        Schema::table('services', function (Blueprint $table) {
            $table->string('audio_file_path')->nullable()->after('youtube_url');
            $table->string('audio_status')->nullable()->after('audio_file_path');
            $table->unsignedTinyInteger('audio_attempts')->default(0)->after('audio_status');
            $table->text('audio_error')->nullable()->after('audio_attempts');
            $table->timestamp('audio_processed_at')->nullable()->after('audio_error');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'audio_file_path',
                'audio_status',
                'audio_attempts',
                'audio_error',
                'audio_processed_at',
            ]);
        });
    }
};
