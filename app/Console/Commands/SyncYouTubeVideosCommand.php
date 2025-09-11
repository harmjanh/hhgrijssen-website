<?php

namespace App\Console\Commands;

use App\Jobs\ProcessYouTubeVideo;
use App\Services\YouTubeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncYouTubeVideosCommand extends Command
{
    protected $signature = 'youtube:sync
                            {--channel-id= : YouTube channel ID to sync from}
                            {--max-results=50 : Maximum number of videos to sync}
                            {--auth : Start OAuth 2.0 authentication flow}
                            {--queue : Dispatch jobs to queue instead of processing immediately}';

    protected $description = 'Sync YouTube videos from a channel to the database using OAuth 2.0 authentication';

    public function handle(YouTubeService $youtubeService)
    {
        $channelId = $this->option('channel-id') ?: config('youtube.channel_id');
        $maxResults = (int) $this->option('max-results');
        $useQueue = $this->option('queue');

        if (!$channelId) {
            $this->error('Channel ID is required. Please provide it via --channel-id option or set YOUTUBE_CHANNEL_ID in your .env file.');
            return 1;
        }

        $this->info("Syncing videos from channel: {$channelId}");
        $this->info("Max results: {$maxResults}");
        $this->info("Using queue: " . ($useQueue ? 'Yes' : 'No'));

        // Check if OAuth authentication is required
        if ($youtubeService->requiresAuthentication()) {
            if ($this->option('auth')) {
                $this->startOAuthFlow($youtubeService);
                return 0;
            } else {
                $this->error('OAuth 2.0 authentication is required to access private YouTube videos.');
                $this->info('Please run: php artisan youtube:sync --auth');
                $this->info('This will start the OAuth 2.0 authentication flow.');
                return 1;
            }
        }

        try {
            $result = $this->syncVideos($youtubeService, $channelId, $maxResults, $useQueue);

            $this->info("Sync completed successfully!");
            $this->info("Added: {$result['new']} new videos");
            $this->info("Updated: {$result['updated']} existing videos");
            $this->info("Total processed: {$result['total']} videos");

            if ($useQueue) {
                $this->info("Jobs have been dispatched to the queue. Run 'php artisan queue:work' to process them.");
            }

        } catch (\Exception $e) {
            $this->error("Error syncing YouTube videos: " . $e->getMessage());
            Log::error('YouTube sync error: ' . $e->getMessage(), [
                'channel_id' => $channelId,
                'max_results' => $maxResults,
                'use_queue' => $useQueue,
                'trace' => $e->getTraceAsString()
            ]);

            // If it's an authentication error, suggest running with --auth
            if (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), 'unauthorized')) {
                $this->info('It appears your authentication token has expired.');
                $this->info('Please run: php artisan youtube:sync --auth');
            }

            return 1;
        }

        return 0;
    }

    /**
     * Sync videos using generator for memory efficiency
     */
    private function syncVideos(YouTubeService $youtubeService, string $channelId, int $maxResults, bool $useQueue): array
    {
        $result = [
            'new' => 0,
            'updated' => 0,
            'total' => 0
        ];

        $progressBar = $this->output->createProgressBar($maxResults);
        $progressBar->start();

        foreach ($youtubeService->getAllVideosFromChannel($channelId, $maxResults) as $videoData) {
            $result['total']++;

            if ($useQueue) {
                // Dispatch job to queue
                ProcessYouTubeVideo::dispatch($videoData);
                $result['new']++; // We assume it's new when dispatching to queue
            } else {
                // Process immediately
                $existingVideo = \App\Models\YouTubeVideo::where('youtube_id', $videoData['youtube_id'])->first();

                if ($existingVideo) {
                    $existingVideo->update($videoData);
                    $result['updated']++;
                } else {
                    \App\Models\YouTubeVideo::create($videoData);
                    $result['new']++;
                }
            }

            $progressBar->advance();

            // Log progress every 50 videos
            if ($result['total'] % 50 === 0) {
                $this->newLine();
                $this->info("Processed {$result['total']} videos...");
            }
        }

        $progressBar->finish();
        $this->newLine();

        return $result;
    }

    /**
     * Start the OAuth 2.0 authentication flow
     */
    private function startOAuthFlow(YouTubeService $youtubeService): void
    {
        $this->info('Starting OAuth 2.0 authentication flow...');

        $authUrl = $youtubeService->getAuthorizationUrl();

        $this->info('Please visit the following URL to authorize the application:');
        $this->line($authUrl);
        $this->newLine();

        $this->info('After authorization, you will be redirected to a page with an authorization code.');
        $this->info('Please copy the authorization code and paste it below.');

        $code = $this->ask('Enter the authorization code');

        if (!$code) {
            $this->error('No authorization code provided. Authentication cancelled.');
            return;
        }

        try {
            $token = $youtubeService->completeOAuthFlow($code);
            $this->info('Authentication successful! Token saved.');
            $this->info('You can now run the sync command without the --auth flag.');
        } catch (\Exception $e) {
            $this->error('Authentication failed: ' . $e->getMessage());
        }
    }
}
