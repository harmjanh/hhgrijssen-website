<?php

namespace Tests\Feature;

use App\Jobs\ProcessServiceAudio;
use App\Models\AgendaItem;
use App\Models\Service;
use App\Services\ServiceAudioProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class ProcessServiceAudioJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_run_stores_audio_path_and_status(): void
    {
        $service = $this->makeService();
        $expectedPath = 'audio/' . $service->getAudioFilename();

        $processor = $this->mock(ServiceAudioProcessor::class, function (MockInterface $mock) use ($expectedPath) {
            $mock->shouldReceive('process')->once()->andReturn($expectedPath);
        });

        (new ProcessServiceAudio($service))->handle($processor);

        $service->refresh();
        $this->assertSame($expectedPath, $service->audio_file_path);
        $this->assertSame(Service::AUDIO_STATUS_COMPLETED, $service->audio_status);
        $this->assertSame(1, $service->audio_attempts);
        $this->assertNotNull($service->audio_processed_at);
        $this->assertNull($service->audio_error);
    }

    public function test_failure_marks_service_as_failed_with_error(): void
    {
        $service = $this->makeService();

        $processor = $this->mock(ServiceAudioProcessor::class, function (MockInterface $mock) {
            $mock->shouldReceive('process')->once()->andThrow(new RuntimeException('yt-dlp exploded'));
        });

        $job = new ProcessServiceAudio($service);

        try {
            $job->handle($processor);
            $this->fail('Expected exception was not thrown.');
        } catch (RuntimeException $e) {
            $job->failed($e);
        }

        $service->refresh();
        $this->assertNull($service->audio_file_path);
        $this->assertSame(Service::AUDIO_STATUS_FAILED, $service->audio_status);
        $this->assertSame(1, $service->audio_attempts);
        $this->assertStringContainsString('yt-dlp exploded', $service->audio_error);
    }

    public function test_service_with_existing_audio_is_skipped(): void
    {
        $service = $this->makeService(['audio_file_path' => 'audio/bestaand.mp3']);

        $processor = $this->mock(ServiceAudioProcessor::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('process');
        });

        (new ProcessServiceAudio($service))->handle($processor);

        $this->assertSame('audio/bestaand.mp3', $service->fresh()->audio_file_path);
    }

    private function makeService(array $attributes = []): Service
    {
        return Service::factory()->create(array_merge([
            'agenda_item_id' => AgendaItem::factory()->create([
                'start_date' => now()->subHours(5),
            ])->id,
            'youtube_url' => 'https://www.youtube.com/watch?v=abc123def45',
        ], $attributes));
    }
}
