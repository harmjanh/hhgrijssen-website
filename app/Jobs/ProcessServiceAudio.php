<?php

namespace App\Jobs;

use App\Models\Service;
use App\Services\ServiceAudioProcessor;
use Closure;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProcessServiceAudio implements ShouldBeUnique, ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    // Must stay above the yt-dlp timeout (3600) inside the processor.
    public $timeout = 3900;

    public function __construct(public Service $service)
    {
        $this->onConnection('media');
        $this->onQueue('media');
    }

    public function uniqueId(): string
    {
        return (string) $this->service->id;
    }

    /**
     * @param  Closure(string):void|null  $onOutput  live process output, used when run synchronously
     */
    public function handle(ServiceAudioProcessor $processor, ?Closure $onOutput = null): void
    {
        $this->service->refresh();

        if (! empty($this->service->audio_file_path)) {
            Log::info("Service {$this->service->id} already has audio, skipping.");

            return;
        }

        $this->service->update([
            'audio_status' => Service::AUDIO_STATUS_PROCESSING,
            'audio_attempts' => $this->service->audio_attempts + 1,
        ]);

        $path = $processor->process($this->service, $onOutput);

        $this->service->update([
            'audio_file_path' => $path,
            'audio_status' => Service::AUDIO_STATUS_COMPLETED,
            'audio_processed_at' => now(),
            'audio_error' => null,
        ]);

        Log::info("Audio for service {$this->service->id} stored at {$path}");
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Audio processing failed for service {$this->service->id}: " . $exception->getMessage());

        $this->service->update([
            'audio_status' => Service::AUDIO_STATUS_FAILED,
            'audio_error' => Str::limit($exception->getMessage(), 1000),
        ]);
    }
}
