<?php

namespace Tests\Feature;

use App\Jobs\ProcessServiceAudio;
use App\Models\AgendaItem;
use App\Models\Service;
use App\Services\ServiceAudioProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class ProcessServiceAudioCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_service_is_processed_synchronously(): void
    {
        $service = $this->makeService();

        $this->mock(ServiceAudioProcessor::class, function (MockInterface $mock) use ($service) {
            $mock->shouldReceive('process')->once()
                ->andReturn('audio/' . $service->getAudioFilename());
        });

        $this->artisan('services:process-audio', ['--service-id' => $service->id])
            ->assertSuccessful();

        $service->refresh();
        $this->assertSame(Service::AUDIO_STATUS_COMPLETED, $service->audio_status);
        $this->assertNotNull($service->audio_file_path);
    }

    public function test_single_ineligible_service_requires_force(): void
    {
        $service = $this->makeService(startDate: now()->subHour());

        $this->mock(ServiceAudioProcessor::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('process');
        });

        $this->artisan('services:process-audio', ['--service-id' => $service->id])
            ->assertFailed();
    }

    public function test_missing_with_queue_dispatches_only_eligible_services(): void
    {
        Queue::fake();

        $eligible = $this->makeService();
        $tooRecent = $this->makeService(startDate: now()->subHour());
        $noUrl = $this->makeService(['youtube_url' => null]);
        $alreadyDone = $this->makeService(['audio_file_path' => 'audio/klaar.mp3']);

        $this->artisan('services:process-audio', ['--missing' => true, '--queue' => true])
            ->assertSuccessful();

        Queue::assertPushed(ProcessServiceAudio::class, 1);
        Queue::assertPushed(
            ProcessServiceAudio::class,
            fn (ProcessServiceAudio $job) => $job->service->id === $eligible->id
        );

        $this->assertSame(Service::AUDIO_STATUS_QUEUED, $eligible->fresh()->audio_status);
        $this->assertNull($tooRecent->fresh()->audio_status);
        $this->assertNull($noUrl->fresh()->audio_status);
        $this->assertNull($alreadyDone->fresh()->audio_status);
    }

    public function test_limit_caps_number_of_dispatched_jobs(): void
    {
        Queue::fake();

        $this->makeService(startDate: now()->subHours(6));
        $this->makeService(startDate: now()->subHours(7));
        $this->makeService(startDate: now()->subHours(8));

        $this->artisan('services:process-audio', [
            '--missing' => true,
            '--queue' => true,
            '--limit' => 2,
        ])->assertSuccessful();

        Queue::assertPushed(ProcessServiceAudio::class, 2);
    }

    public function test_interactive_mode_respects_confirmation(): void
    {
        $process = $this->makeService(startDate: now()->subHours(6));
        $skip = $this->makeService(startDate: now()->subHours(5));

        $this->mock(ServiceAudioProcessor::class, function (MockInterface $mock) use ($process) {
            $mock->shouldReceive('process')->once()
                ->andReturn('audio/' . $process->getAudioFilename());
        });

        $this->artisan('services:process-audio')
            ->expectsConfirmation('Deze dienst verwerken?', 'yes')
            ->expectsConfirmation('Deze dienst verwerken?', 'no')
            ->assertSuccessful();

        $this->assertSame(Service::AUDIO_STATUS_COMPLETED, $process->fresh()->audio_status);
        $this->assertNull($skip->fresh()->audio_status);
    }

    public function test_missing_without_services_reports_nothing_to_do(): void
    {
        $this->artisan('services:process-audio', ['--missing' => true])
            ->expectsOutput('Geen diensten gevonden die audio missen.')
            ->assertSuccessful();
    }

    private function makeService(array $attributes = [], ?string $startDate = null): Service
    {
        return Service::factory()->create(array_merge([
            'agenda_item_id' => AgendaItem::factory()->create([
                'start_date' => $startDate ?? now()->subHours(5),
            ])->id,
            'youtube_url' => 'https://www.youtube.com/watch?v=abc123def45',
        ], $attributes));
    }
}
