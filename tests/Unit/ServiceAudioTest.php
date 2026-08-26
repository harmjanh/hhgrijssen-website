<?php

namespace Tests\Unit;

use App\Models\AgendaItem;
use App\Models\Service;
use App\Models\YouTubeVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_audio_filename_contains_service_date_time_and_id(): void
    {
        $service = $this->makeService(startDate: '2026-08-01 09:30:00');

        $this->assertSame(
            "dienst-2026-08-01-0930-{$service->id}.mp3",
            $service->getAudioFilename()
        );
    }

    public function test_service_started_more_than_four_hours_ago_needs_audio(): void
    {
        $service = $this->makeService(startDate: now()->subHours(4)->subMinute());

        $this->assertTrue(Service::needsAudio()->whereKey($service->id)->exists());
    }

    public function test_service_started_less_than_four_hours_ago_does_not_need_audio(): void
    {
        $service = $this->makeService(startDate: now()->subHours(3)->subMinutes(59));

        $this->assertFalse(Service::needsAudio()->whereKey($service->id)->exists());
    }

    public function test_service_without_youtube_url_does_not_need_audio(): void
    {
        $service = $this->makeService(['youtube_url' => null]);

        $this->assertFalse(Service::needsAudio()->whereKey($service->id)->exists());
    }

    public function test_service_with_audio_does_not_need_audio(): void
    {
        $service = $this->makeService(['audio_file_path' => 'audio/dienst-2026-08-01-0930-1.mp3']);

        $this->assertFalse(Service::needsAudio()->whereKey($service->id)->exists());
    }

    public function test_queued_or_processing_service_does_not_need_audio(): void
    {
        foreach ([Service::AUDIO_STATUS_QUEUED, Service::AUDIO_STATUS_PROCESSING] as $status) {
            $service = $this->makeService(['audio_status' => $status]);

            $this->assertFalse(
                Service::needsAudio()->whereKey($service->id)->exists(),
                "Service with status {$status} should not need audio."
            );
        }
    }

    public function test_failed_service_is_retried_until_attempts_are_exhausted(): void
    {
        $retryable = $this->makeService([
            'audio_status' => Service::AUDIO_STATUS_FAILED,
            'audio_attempts' => Service::MAX_AUDIO_ATTEMPTS - 1,
        ]);
        $exhausted = $this->makeService([
            'audio_status' => Service::AUDIO_STATUS_FAILED,
            'audio_attempts' => Service::MAX_AUDIO_ATTEMPTS,
        ]);

        $this->assertTrue(Service::needsAudio()->whereKey($retryable->id)->exists());
        $this->assertFalse(Service::needsAudio()->whereKey($exhausted->id)->exists());
    }

    public function test_service_with_legacy_video_audio_does_not_need_audio(): void
    {
        $video = YouTubeVideo::create([
            'youtube_id' => 'abc123def45',
            'url' => 'https://www.youtube.com/watch?v=abc123def45',
            'title' => 'Dienst 01-08-2026 09:30',
            'download_status' => 'completed',
            'audio_file_path' => 'audio/abc123def45.mp3',
        ]);

        $service = $this->makeService(['youtube_video_id' => $video->id]);

        $this->assertFalse(Service::needsAudio()->whereKey($service->id)->exists());
        $this->assertTrue($service->hasAudio());
    }

    public function test_has_audio_prefers_own_audio_file_path(): void
    {
        $service = $this->makeService(['audio_file_path' => 'audio/dienst-2026-08-01-0930-1.mp3']);

        $this->assertTrue($service->hasAudio());
        $this->assertFalse($this->makeService()->hasAudio());
    }

    private function makeService(array $serviceAttributes = [], ?string $startDate = null): Service
    {
        return Service::factory()->create(array_merge([
            'agenda_item_id' => AgendaItem::factory()->create([
                'start_date' => $startDate ?? now()->subHours(5),
            ])->id,
            'youtube_url' => 'https://www.youtube.com/watch?v=abc123def45',
        ], $serviceAttributes));
    }
}
