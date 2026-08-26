<?php

namespace App\Services;

use App\Models\Service;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Downloads the audio of a service's YouTube video as MP3 and uploads it
 * to the "youtube" disk (S3 in production). Returns the stored path.
 */
class ServiceAudioProcessor
{
    private const TEMP_DIRECTORY = 'youtube-temp';

    private const DOWNLOAD_TIMEOUT = 3600;

    /**
     * @param  Closure(string):void|null  $onOutput  receives live yt-dlp/ffmpeg output
     * @return string the path on the "youtube" disk, e.g. "audio/dienst-2026-08-01-0930-123.mp3"
     *
     * @throws RuntimeException on any failure
     */
    public function process(Service $service, ?Closure $onOutput = null): string
    {
        $this->assertProcessable($service);

        Storage::disk('local')->makeDirectory(self::TEMP_DIRECTORY);

        try {
            $localFile = $this->downloadAudio($service, $onOutput);

            return $this->uploadToBucket($service, $localFile);
        } finally {
            $this->cleanupTempFiles($service);
        }
    }

    private function assertProcessable(Service $service): void
    {
        if (empty($service->youtube_url)) {
            throw new RuntimeException("Service {$service->id} has no YouTube url.");
        }

        if (! $service->start_date) {
            throw new RuntimeException("Service {$service->id} has no start date (missing agenda item).");
        }
    }

    /**
     * Download audio-only via yt-dlp and convert to mp3 (yt-dlp invokes ffmpeg).
     * Returns the absolute path of the local mp3 file.
     */
    private function downloadAudio(Service $service, ?Closure $onOutput): string
    {
        $outputTemplate = Storage::disk('local')->path(
            self::TEMP_DIRECTORY . "/service-{$service->id}.%(ext)s"
        );

        $command = [
            config('youtube.yt_dlp_path'),
            '-x',
            '--audio-format', 'mp3',
            '--audio-quality', '192K',
            '--ffmpeg-location', config('youtube.ffmpeg_path'),
            '--no-playlist',
            '-o', $outputTemplate,
        ];

        $this->addCookies($command);

        $command[] = $service->youtube_url;

        $result = Process::timeout(self::DOWNLOAD_TIMEOUT)->run(
            $command,
            $onOutput ? fn (string $type, string $output) => $onOutput($output) : null
        );

        if (! $result->successful()) {
            $errorOutput = $result->errorOutput();

            if (str_contains($errorOutput, 'Private video') || str_contains($errorOutput, 'Sign in')) {
                throw new RuntimeException(
                    "Video for service {$service->id} requires authentication (private video)."
                );
            }

            throw new RuntimeException("yt-dlp failed for service {$service->id}: " . $errorOutput);
        }

        $localFile = Storage::disk('local')->path(
            self::TEMP_DIRECTORY . "/service-{$service->id}.mp3"
        );

        if (! is_file($localFile) || filesize($localFile) === 0) {
            throw new RuntimeException("Downloaded mp3 not found for service {$service->id}.");
        }

        return $localFile;
    }

    /**
     * Private videos require cookies of an account with access; yt-dlp
     * no longer supports OAuth bearer tokens for YouTube downloads.
     */
    private function addCookies(array &$command): void
    {
        $cookiesPath = config('youtube.cookies_path');

        if ($cookiesPath && is_file($cookiesPath)) {
            $command[] = '--cookies';
            $command[] = $cookiesPath;
        }
    }

    private function uploadToBucket(Service $service, string $localFile): string
    {
        $path = 'audio/' . $service->getAudioFilename();
        $disk = Storage::disk('youtube');

        $stream = fopen($localFile, 'r');

        try {
            $disk->writeStream($path, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        // The youtube disk may be configured with 'throw' => false,
        // so verify the upload explicitly.
        if (! $disk->exists($path) || $disk->size($path) === 0) {
            throw new RuntimeException("Upload verification failed for {$path}.");
        }

        Log::info("Uploaded audio for service {$service->id} to {$path}");

        return $path;
    }

    private function cleanupTempFiles(Service $service): void
    {
        $pattern = Storage::disk('local')->path(self::TEMP_DIRECTORY . "/service-{$service->id}.*");

        foreach (glob($pattern) ?: [] as $file) {
            @unlink($file);
        }
    }
}
