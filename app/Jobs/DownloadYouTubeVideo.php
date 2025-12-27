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

            // Create local temp directories if they don't exist (for downloading and processing)
            $this->ensureLocalDirectoriesExist();

            // Download video to local temp directory
            $localVideoFile = $this->downloadVideo();

            // Extract audio from video (local temp)
            $localAudioFile = $this->extractAudio($localVideoFile);

            // Upload video to S3
            $s3VideoPath = $this->uploadToS3($localVideoFile, $this->downloadPath);

            // Upload audio to S3
            $s3AudioPath = $this->uploadToS3($localAudioFile, $this->audioPath);

            // Clean up local files
            $this->cleanupLocalFiles($localVideoFile, $localAudioFile);

            // Update video model with S3 file paths
            $this->updateVideoModel($s3VideoPath, $s3AudioPath);

            Log::info("Successfully downloaded and uploaded video to S3: {$this->video->youtube_id}");

        } catch (\Exception $e) {
            Log::error("Failed to download video {$this->video->youtube_id}: " . $e->getMessage());
            $this->updateVideoModel(null, null, 'failed');
            throw $e;
        }
    }

    /**
     * Ensure local temp directories exist for downloading and processing
     */
    private function ensureLocalDirectoriesExist(): void
    {
        $localDisk = Storage::disk('local');
        $localDisk->makeDirectory('youtube-temp/' . $this->downloadPath);
        $localDisk->makeDirectory('youtube-temp/' . $this->audioPath);
    }


    /**
     * Download the video using yt-dlp to local temp directory
     */
    private function downloadVideo(): string
    {
        $filename = $this->video->youtube_id . '.%(ext)s';
        $outputPath = Storage::disk('local')->path('youtube-temp/' . $this->downloadPath . '/' . $filename);

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
        $files = glob(Storage::disk('local')->path('youtube-temp/' . $this->downloadPath . '/' . $this->video->youtube_id . '.*'));

        if (empty($files)) {
            throw new \Exception("Downloaded file not found for video: {$this->video->youtube_id}");
        }

        return basename($files[0]);
    }

    /**
     * Extract audio from video using ffmpeg (local temp)
     */
    private function extractAudio(string $videoFile): string
    {
        $videoPath = Storage::disk('local')->path('youtube-temp/' . $this->downloadPath . '/' . $videoFile);
        $audioFilename = $this->video->youtube_id . '.mp3';
        $audioPath = Storage::disk('local')->path('youtube-temp/' . $this->audioPath . '/' . $audioFilename);

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
     * Upload file to S3
     */
    private function uploadToS3(string $localFilename, string $s3Directory): string
    {
        $localDisk = Storage::disk('local');
        $s3Disk = Storage::disk('youtube');

        // Determine the local path based on directory
        $localPath = 'youtube-temp/' . ($s3Directory === $this->downloadPath ? $this->downloadPath : $this->audioPath) . '/' . $localFilename;

        // Check if file exists locally
        if (!$localDisk->exists($localPath)) {
            throw new \Exception("Local file not found: {$localPath}");
        }

        // S3 path (same structure as local)
        $s3Path = $s3Directory . '/' . $localFilename;

        // Read local file and upload to S3
        $fileContents = $localDisk->get($localPath);
        $s3Disk->put($s3Path, $fileContents);

        Log::info("Uploaded {$localFilename} to S3: {$s3Path}");

        return $s3Path;
    }

    /**
     * Clean up local temporary files
     */
    private function cleanupLocalFiles(string $videoFile, string $audioFile): void
    {
        $localDisk = Storage::disk('local');

        try {
            // Delete video file
            $videoPath = 'youtube-temp/' . $this->downloadPath . '/' . $videoFile;
            if ($localDisk->exists($videoPath)) {
                $localDisk->delete($videoPath);
                Log::info("Deleted local video file: {$videoPath}");
            }

            // Delete audio file
            $audioPath = 'youtube-temp/' . $this->audioPath . '/' . $audioFile;
            if ($localDisk->exists($audioPath)) {
                $localDisk->delete($audioPath);
                Log::info("Deleted local audio file: {$audioPath}");
            }
        } catch (\Exception $e) {
            Log::warning("Failed to cleanup local files: " . $e->getMessage());
            // Don't throw - cleanup failure shouldn't fail the job
        }
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
