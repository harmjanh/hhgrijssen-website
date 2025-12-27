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

    public function services()
    {
        return $this->hasMany(Service::class);
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
     * Get the full path to the video file (works with both local and S3)
     */
    public function getVideoFilePath(): ?string
    {
        if (!$this->video_file_path) {
            return null;
        }

        $disk = Storage::disk('youtube');
        
        // For S3, return the URL or path
        if (config('filesystems.disks.youtube.driver') === 's3') {
            return $disk->url($this->video_file_path);
        }

        // For local storage, return the full path
        return $disk->path($this->video_file_path);
    }

    /**
     * Get the full path to the audio file (works with both local and S3)
     */
    public function getAudioFilePath(): ?string
    {
        if (!$this->audio_file_path) {
            return null;
        }

        $disk = Storage::disk('youtube');
        
        // For S3, return the URL or path
        if (config('filesystems.disks.youtube.driver') === 's3') {
            return $disk->url($this->audio_file_path);
        }

        // For local storage, return the full path
        return $disk->path($this->audio_file_path);
    }
}


