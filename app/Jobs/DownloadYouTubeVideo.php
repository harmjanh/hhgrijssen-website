<?php

namespace App\Jobs;

use App\Models\YouTubeVideo;
use App\Services\YouTubeOAuthService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class DownloadYouTubeVideo implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public YouTubeVideo $video;
    public string $downloadPath;
    public string $audioPath;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(YouTubeVideo $video)
    {
        $this->video = $video;
        $this->downloadPath = 'videos';
        $this->audioPath = 'audio';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting download for video: {$this->video->youtube_id}");

            // Create directories if they don't exist
            $this->ensureDirectoriesExist();

            // Download video
            $videoFile = $this->downloadVideo();

            // Extract audio from video
            $audioFile = $this->extractAudio($videoFile);

            // Update video model with file paths
            $this->updateVideoModel($videoFile, $audioFile);

            Log::info("Successfully downloaded video: {$this->video->youtube_id}");

        } catch (\Exception $e) {
            Log::error("Failed to download video {$this->video->youtube_id}: " . $e->getMessage());
            $this->updateVideoModel(null, null, 'failed');
            throw $e;
        }
    }

    /**
     * Ensure download directories exist
     */
    private function ensureDirectoriesExist(): void
    {
        Storage::disk('youtube')->makeDirectory($this->downloadPath);
        Storage::disk('youtube')->makeDirectory($this->audioPath);
    }


    /**
     * Download the video using yt-dlp
     */
    private function downloadVideo(): string
    {
        $filename = $this->video->youtube_id . '.%(ext)s';
        $outputPath = Storage::disk('youtube')->path($this->downloadPath . '/' . $filename);

        $ytDlpPath = config('youtube.yt_dlp_path');
        $command = [
            $ytDlpPath,
            '--output', $outputPath,
            '--format', 'best[height<=1080]', // Download best quality up to 1080p
            '--no-playlist',
        ];

        // Add OAuth authentication
        $this->addOAuthAuthentication($command);

        $command[] = $this->video->url;

        $process = new Process($command);
        $process->setTimeout(3600); // 1 hour timeout
        $process->run();

        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();

            // Check for specific authentication errors
            if (str_contains($errorOutput, 'Private video') || str_contains($errorOutput, 'Sign in')) {
                throw new \Exception("Authentication required for private video. Please ensure OAuth is configured and you have access to this video.");
            }

            throw new \Exception("Failed to download video: " . $errorOutput);
        }

        // Find the actual downloaded file (since we used %(ext)s placeholder)
        $files = glob(Storage::disk('youtube')->path($this->downloadPath . '/' . $this->video->youtube_id . '.*'));

        if (empty($files)) {
            throw new \Exception("Downloaded file not found for video: {$this->video->youtube_id}");
        }

        return basename($files[0]);
    }

    /**
     * Extract audio from video using ffmpeg
     */
    private function extractAudio(string $videoFile): string
    {
        $videoPath = Storage::disk('youtube')->path($this->downloadPath . '/' . $videoFile);
        $audioFilename = $this->video->youtube_id . '.mp3';
        $audioPath = Storage::disk('youtube')->path($this->audioPath . '/' . $audioFilename);

        $command = [
            'ffmpeg',
            '-i', $videoPath,
            '-vn', // No video
            '-acodec', 'mp3',
            '-ab', '192k', // Audio bitrate
            '-ar', '44100', // Sample rate
            '-y', // Overwrite output file
            $audioPath
        ];

        $process = new Process($command);
        $process->setTimeout(1800); // 30 minutes timeout
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("Failed to extract audio: " . $process->getErrorOutput());
        }

        return $audioFilename;
    }

    /**
     * Add OAuth authentication to yt-dlp command
     */
    private function addOAuthAuthentication(array &$command): void
    {
        try {
            $oauthService = app(YouTubeOAuthService::class);
            $token = $oauthService->getStoredToken();

            if ($token && isset($token['access_token'])) {
                // Use the access token for authentication
                $command[] = '--add-header';
                $command[] = 'Authorization: Bearer ' . $token['access_token'];
                Log::info("Using OAuth access token for authentication");
            } else {
                throw new \Exception("No valid OAuth token found");
            }
        } catch (\Exception $e) {
            Log::warning("OAuth authentication failed: " . $e->getMessage());
            throw new \Exception("OAuth authentication failed: " . $e->getMessage());
        }
    }

    /**
     * Update the video model with file paths and status
     */
    private function updateVideoModel(?string $videoFile, ?string $audioFile, string $status = 'completed'): void
    {
        $updateData = [
            'download_status' => $status,
            'downloaded_at' => now(),
        ];

        if ($videoFile) {
            $updateData['video_file_path'] = $this->downloadPath . '/' . $videoFile;
        }

        if ($audioFile) {
            $updateData['audio_file_path'] = $this->audioPath . '/' . $audioFile;
        }

        $this->video->update($updateData);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Download job failed for video {$this->video->youtube_id}: " . $exception->getMessage());
        $this->updateVideoModel(null, null, 'failed');
    }
}
