<?php

namespace App\Console\Commands;

use App\Jobs\ProcessServiceAudio;
use App\Models\Service;
use App\Services\ServiceAudioProcessor;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class ProcessServiceAudioCommand extends Command
{
    protected $signature = 'services:process-audio
        {--service-id= : Verwerk één specifieke dienst}
        {--missing : Verwerk alle in aanmerking komende diensten (non-interactief)}
        {--queue : Zet jobs op de media-queue in plaats van direct uitvoeren}
        {--limit= : Maximum aantal diensten per run}
        {--force : Ook verwerken als er al audio is of de pogingen op zijn}';

    protected $description = 'Download dienst-audio van YouTube als mp3 en zet deze op de bucket';

    public function handle(ServiceAudioProcessor $processor): int
    {
        if ($this->option('service-id')) {
            return $this->processSingleService($processor);
        }

        if ($this->option('missing')) {
            return $this->processMissingServices($processor);
        }

        return $this->processInteractively($processor);
    }

    private function processSingleService(ServiceAudioProcessor $processor): int
    {
        $service = Service::with(['agendaItem', 'youtubeVideo'])->find($this->option('service-id'));

        if (! $service) {
            $this->error("Dienst {$this->option('service-id')} niet gevonden.");

            return self::FAILURE;
        }

        if (! $this->option('force') && ! Service::needsAudio()->whereKey($service->id)->exists()) {
            $this->error(
                "Dienst {$service->id} komt niet in aanmerking (al audio, geen YouTube-url, "
                . 'nog geen 4 uur gestart of pogingen op). Gebruik --force om toch te verwerken.'
            );

            return self::FAILURE;
        }

        $this->showServiceInfo($service);

        return $this->processService($service, $processor) ? self::SUCCESS : self::FAILURE;
    }

    private function processMissingServices(ServiceAudioProcessor $processor): int
    {
        $services = $this->eligibleServices()->get();

        if ($services->isEmpty()) {
            $this->info('Geen diensten gevonden die audio missen.');

            return self::SUCCESS;
        }

        $this->info("{$services->count()} dienst(en) te verwerken.");

        if ($this->option('queue')) {
            foreach ($services as $service) {
                $service->update(['audio_status' => Service::AUDIO_STATUS_QUEUED]);
                ProcessServiceAudio::dispatch($service);
                $this->line("Dienst {$service->id} ({$service->formatted_start_date}) op de media-queue gezet.");
            }

            return self::SUCCESS;
        }

        $failures = 0;

        foreach ($services as $service) {
            $this->showServiceInfo($service);

            if (! $this->processService($service, $processor)) {
                $failures++;
            }
        }

        $this->newLine();
        $this->info(sprintf('Klaar: %d gelukt, %d mislukt.', $services->count() - $failures, $failures));

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function processInteractively(ServiceAudioProcessor $processor): int
    {
        $services = $this->eligibleServices()->get();

        if ($services->isEmpty()) {
            $this->info('Geen diensten gevonden die audio missen.');

            return self::SUCCESS;
        }

        $this->info("{$services->count()} dienst(en) zonder audio gevonden.");

        foreach ($services as $service) {
            $this->showServiceInfo($service);

            if (! $this->confirm('Deze dienst verwerken?', true)) {
                $this->line('Overgeslagen.');

                continue;
            }

            if ($this->option('queue')) {
                $service->update(['audio_status' => Service::AUDIO_STATUS_QUEUED]);
                ProcessServiceAudio::dispatch($service);
                $this->info('Op de media-queue gezet.');

                continue;
            }

            $this->processService($service, $processor);
        }

        return self::SUCCESS;
    }

    /**
     * Runs the job inline (same code path as the queue) with live output.
     */
    private function processService(Service $service, ServiceAudioProcessor $processor): bool
    {
        $job = new ProcessServiceAudio($service);

        try {
            $job->handle($processor, fn (string $output) => $this->output->write($output));

            $this->info("Audio opgeslagen: {$service->fresh()->audio_file_path}");

            return true;
        } catch (Throwable $e) {
            $job->failed($e);

            $this->error("Mislukt: {$e->getMessage()}");

            return false;
        }
    }

    private function eligibleServices(): Builder
    {
        $query = $this->option('force')
            ? Service::query()
                ->whereNotNull('youtube_url')
                ->where('youtube_url', '!=', '')
                ->whereNull('audio_file_path')
                ->whereHas('agendaItem', fn (Builder $q) => $q->where('start_date', '<=', now()->subHours(4)))
            : Service::needsAudio();

        $query = $query
            ->with(['agendaItem', 'youtubeVideo'])
            ->join('agenda_items', 'services.agenda_item_id', '=', 'agenda_items.id')
            ->select('services.*')
            ->orderBy('agenda_items.start_date');

        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        return $query;
    }

    private function showServiceInfo(Service $service): void
    {
        $this->newLine();
        $this->table(
            ['Id', 'Datum/tijd', 'Voorganger', 'YouTube', 'Status', 'Pogingen'],
            [[
                $service->id,
                $service->formatted_start_date ?? 'onbekend',
                $service->pastor ?: '-',
                $service->youtube_url ?: '-',
                $service->audio_status ?? 'nieuw',
                $service->audio_attempts,
            ]]
        );
    }
}
