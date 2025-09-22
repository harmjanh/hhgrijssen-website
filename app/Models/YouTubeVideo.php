<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class YouTubeVideo extends Model
{
    protected $guarded = [];

    protected $table = 'youtube_videos';

    protected $casts = [
        'published_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_hidden', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeDownloaded($query)
    {
        return $query->where('download_status', 'completed');
    }

    public function scopeNotDownloaded($query)
    {
        return $query->whereNull('download_status')->orWhere('download_status', '!=', 'completed');
    }

    public function scopeDownloadFailed($query)
    {
        return $query->where('download_status', 'failed');
    }

    /**
     * Check if video has been downloaded
     */
    public function isDownloaded(): bool
    {
        return $this->download_status === 'completed' && !empty($this->video_file_path);
    }

    /**
     * Check if audio has been extracted
     */
    public function hasAudio(): bool
    {
        return $this->download_status === 'completed' && !empty($this->audio_file_path);
    }

    /**
     * Get the full path to the video file
     */
    public function getVideoFilePath(): ?string
    {
        return $this->video_file_path ? Storage::disk('youtube')->path($this->video_file_path) : null;
    }

    /**
     * Get the full path to the audio file
     */
    public function getAudioFilePath(): ?string
    {
        return $this->audio_file_path ? Storage::disk('youtube')->path($this->audio_file_path) : null;
    }
}


