<?php

namespace Tests\Feature;

use App\Models\AgendaItem;
use App\Models\Service;
use App\Models\YouTubeVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StreamAudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_streams_service_audio_from_local_disk(): void
    {
        Storage::fake('youtube');

        $service = $this->makeService(['audio_file_path' => 'audio/dienst-2026-08-01-0930-1.mp3']);
        Storage::disk('youtube')->put($service->audio_file_path, 'mp3-bytes');

        $response = $this->get("/audio/{$service->id}");

        $response->assertOk();
        $this->assertSame('audio/mpeg', $response->headers->get('Content-Type'));
    }

    public function test_redirects_to_presigned_url_when_disk_is_s3(): void
    {
        Storage::fake('youtube');
        config(['filesystems.disks.youtube.driver' => 's3']);

        Storage::disk('youtube')->buildTemporaryUrlsUsing(
            fn (string $path) => 'https://bucket.example.com/' . $path
        );

        $service = $this->makeService(['audio_file_path' => 'audio/dienst-2026-08-01-0930-1.mp3']);
        Storage::disk('youtube')->put($service->audio_file_path, 'mp3-bytes');

        $this->get("/audio/{$service->id}")
            ->assertRedirect('https://bucket.example.com/audio/dienst-2026-08-01-0930-1.mp3');
    }

    public function test_falls_back_to_legacy_video_audio(): void
    {
        Storage::fake('youtube');

        $video = YouTubeVideo::create([
            'youtube_id' => 'abc123def45',
            'url' => 'https://www.youtube.com/watch?v=abc123def45',
            'title' => 'Dienst',
            'download_status' => 'completed',
            'audio_file_path' => 'audio/abc123def45.mp3',
        ]);
        Storage::disk('youtube')->put('audio/abc123def45.mp3', 'legacy-bytes');

        $service = $this->makeService(['youtube_video_id' => $video->id]);

        $this->get("/audio/{$service->id}")->assertOk();
    }

    public function test_returns_404_when_no_audio_exists(): void
    {
        Storage::fake('youtube');

        $service = $this->makeService();

        $this->get("/audio/{$service->id}")->assertNotFound();
    }

    public function test_returns_404_when_file_is_missing_on_disk(): void
    {
        Storage::fake('youtube');

        $service = $this->makeService(['audio_file_path' => 'audio/verdwenen.mp3']);

        $this->get("/audio/{$service->id}")->assertNotFound();
    }

    private function makeService(array $attributes = []): Service
    {
        return Service::factory()->create(array_merge([
            'agenda_item_id' => AgendaItem::factory()->create([
                'start_date' => now()->subDay(),
            ])->id,
            'youtube_url' => 'https://www.youtube.com/watch?v=abc123def45',
        ], $attributes));
    }
}
