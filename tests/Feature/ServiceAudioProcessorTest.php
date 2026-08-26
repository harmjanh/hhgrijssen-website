<?php

namespace Tests\Feature;

use App\Models\AgendaItem;
use App\Models\Service;
use App\Services\ServiceAudioProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ServiceAudioProcessorTest extends TestCase
{
    use RefreshDatabase;

    public function test_downloads_audio_and_uploads_mp3_to_bucket(): void
    {
        Storage::fake('local');
        Storage::fake('youtube');
        Process::fake();

        $service = $this->makeService();
        $this->fakeDownloadedMp3($service);

        $path = app(ServiceAudioProcessor::class)->process($service);

        $this->assertSame("audio/dienst-2026-08-01-0930-{$service->id}.mp3", $path);
        Storage::disk('youtube')->assertExists($path);
        $this->assertSame('fake-mp3-bytes', Storage::disk('youtube')->get($path));

        Process::assertRan(function ($process) use ($service) {
            $command = is_array($process->command) ? implode(' ', $process->command) : $process->command;

            return str_contains($command, '-x')
                && str_contains($command, '--audio-format mp3')
                && str_contains($command, '--no-playlist')
                && str_contains($command, $service->youtube_url);
        });

        // Temp file is cleaned up afterwards.
        Storage::disk('local')->assertMissing("youtube-temp/service-{$service->id}.mp3");
    }

    public function test_throws_when_yt_dlp_fails(): void
    {
        Storage::fake('local');
        Storage::fake('youtube');
        Process::fake([
            '*' => Process::result(output: '', errorOutput: 'ERROR: nope', exitCode: 1),
        ]);

        $service = $this->makeService();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('yt-dlp failed');

        app(ServiceAudioProcessor::class)->process($service);
    }

    public function test_throws_clear_error_for_private_videos(): void
    {
        Storage::fake('local');
        Storage::fake('youtube');
        Process::fake([
            '*' => Process::result(output: '', errorOutput: 'ERROR: Private video. Sign in', exitCode: 1),
        ]);

        $service = $this->makeService();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('private video');

        app(ServiceAudioProcessor::class)->process($service);
    }

    public function test_throws_when_download_produced_no_file(): void
    {
        Storage::fake('local');
        Storage::fake('youtube');
        Process::fake();

        $service = $this->makeService();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Downloaded mp3 not found');

        app(ServiceAudioProcessor::class)->process($service);
    }

    public function test_throws_when_service_has_no_youtube_url(): void
    {
        $service = $this->makeService(['youtube_url' => null]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no YouTube url');

        app(ServiceAudioProcessor::class)->process($service);
    }

    private function makeService(array $attributes = []): Service
    {
        return Service::factory()->create(array_merge([
            'agenda_item_id' => AgendaItem::factory()->create([
                'start_date' => '2026-08-01 09:30:00',
            ])->id,
            'youtube_url' => 'https://www.youtube.com/watch?v=abc123def45',
        ], $attributes));
    }

    private function fakeDownloadedMp3(Service $service): void
    {
        // Process::fake() doesn't create files, so simulate yt-dlp's output.
        Storage::disk('local')->put("youtube-temp/service-{$service->id}.mp3", 'fake-mp3-bytes');
    }
}
