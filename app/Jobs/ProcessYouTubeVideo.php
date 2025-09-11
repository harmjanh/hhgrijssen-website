<?php

namespace App\Jobs;

use App\Models\YouTubeVideo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessYouTubeVideo implements ShouldQueue
{
    use Queueable;

    public array $videoData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $videoData)
    {
        $this->videoData = $videoData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $existingVideo = YouTubeVideo::where('youtube_id', $this->videoData['youtube_id'])->first();

            if ($existingVideo) {
                $existingVideo->update($this->videoData);
                Log::info("Updated YouTube video: {$this->videoData['youtube_id']}");
            } else {
                YouTubeVideo::create($this->videoData);
                Log::info("Created new YouTube video: {$this->videoData['youtube_id']}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to process YouTube video {$this->videoData['youtube_id']}: " . $e->getMessage());
            throw $e;
        }
    }
}
