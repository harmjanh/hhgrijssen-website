<?php

namespace App\Services;

use Google\Service\YouTube;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    private YouTubeOAuthService $oauthService;

    public function __construct(YouTubeOAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    /**
     * Get channel's uploads playlist ID
     */
    public function getChannelUploadsPlaylistId(string $channelId): string
    {
        $youtube = $this->oauthService->getAuthenticatedYouTubeService();

        $channelResponse = $youtube->channels->listChannels('contentDetails', [
            'id' => $channelId
        ]);

        if (empty($channelResponse->getItems())) {
            throw new \Exception("Channel not found: {$channelId}");
        }

        return $channelResponse->getItems()[0]
            ->getContentDetails()
            ->getRelatedPlaylists()
            ->getUploads();
    }

    /**
     * Get video IDs from a playlist (generator for memory efficiency)
     */
    public function getVideoIdsFromPlaylist(string $playlistId, int $maxResults = 50): \Generator
    {
        $youtube = $this->oauthService->getAuthenticatedYouTubeService();
        $nextPageToken = null;
        $totalProcessed = 0;

        do {
            $params = [
                'playlistId' => $playlistId,
                'maxResults' => min(50, $maxResults - $totalProcessed), // YouTube API max is 50
            ];

            if ($nextPageToken) {
                $params['pageToken'] = $nextPageToken;
            }

            $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', $params);

            foreach ($playlistItemsResponse->getItems() as $item) {
                if ($totalProcessed >= $maxResults) {
                    break 2;
                }

                yield $item->getSnippet()->getResourceId()->getVideoId();
                $totalProcessed++;
            }

            $nextPageToken = $playlistItemsResponse->getNextPageToken();

        } while ($nextPageToken && $totalProcessed < $maxResults);
    }

    /**
     * Get detailed video information by IDs (generator for memory efficiency)
     */
    public function getVideosByIds(array $videoIds): \Generator
    {
        $youtube = $this->oauthService->getAuthenticatedYouTubeService();

        // Process videos in batches of 50 (YouTube API limit)
        $batches = array_chunk($videoIds, 50);

        foreach ($batches as $batch) {
            $videosResponse = $youtube->videos->listVideos('snippet,status', [
                'id' => implode(',', $batch)
            ]);

            foreach ($videosResponse->getItems() as $video) {
                yield $this->extractVideoData($video);
            }
        }
    }

    /**
     * Get all videos from a channel (generator for memory efficiency)
     */
    public function getAllVideosFromChannel(string $channelId, int $maxResults = 50): \Generator
    {
        $uploadsPlaylistId = $this->getChannelUploadsPlaylistId($channelId);

        $videoIds = [];
        foreach ($this->getVideoIdsFromPlaylist($uploadsPlaylistId, $maxResults) as $videoId) {
            $videoIds[] = $videoId;

            // Process in batches to avoid memory issues
            if (count($videoIds) >= 50) {
                foreach ($this->getVideosByIds($videoIds) as $videoData) {
                    yield $videoData;
                }
                $videoIds = [];
            }
        }

        // Process remaining videos
        if (!empty($videoIds)) {
            foreach ($this->getVideosByIds($videoIds) as $videoData) {
                yield $videoData;
            }
        }
    }

    /**
     * Extract video data from YouTube API response
     */
    private function extractVideoData($video): array
    {
        $snippet = $video->getSnippet();
        $status = $video->getStatus();

        return [
            'youtube_id' => $video->getId(),
            'title' => $snippet->getTitle(),
            'description' => $snippet->getDescription(),
            'thumbnail_url' => $snippet->getThumbnails()->getDefault()->getUrl(),
            'published_at' => $snippet->getPublishedAt(),
            'url' => 'https://www.youtube.com/watch?v=' . $video->getId(),
            'is_published' => $status->getPrivacyStatus() === 'public',
            'is_hidden' => $status->getPrivacyStatus() === 'private',
        ];
    }

    /**
     * Check if OAuth authentication is required
     */
    public function requiresAuthentication(): bool
    {
        return !$this->oauthService->hasValidCredentials();
    }

    /**
     * Get authorization URL for OAuth flow
     */
    public function getAuthorizationUrl(): string
    {
        return $this->oauthService->getAuthorizationUrl();
    }

    /**
     * Complete OAuth flow with authorization code
     */
    public function completeOAuthFlow(string $code): array
    {
        return $this->oauthService->exchangeCodeForToken($code);
    }
}
