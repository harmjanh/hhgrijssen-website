<?php

namespace App\Console\Commands;

use App\Models\AgendaItem;
use App\Models\Service;
use App\Services\ServiceTitleService;
use Illuminate\Console\Command;

class CreateMissingServicesCommand extends Command
{
    protected $signature = 'services:create-missing {--dry-run : Show what would be created without actually creating}';

    protected $description = 'Create services for agenda items that should have them but don\'t';

    public function handle()
    {
        $serviceTitles = ServiceTitleService::getServiceTitles();

        $agendaItemsWithoutServices = AgendaItem::whereIn('title', $serviceTitles)
            ->whereDoesntHave('service')
            ->get();

        if ($agendaItemsWithoutServices->isEmpty()) {
            $this->info('No agenda items found that need services.');
            return 0;
        }

        $this->info("Found {$agendaItemsWithoutServices->count()} agenda items that need services:");

        foreach ($agendaItemsWithoutServices as $agendaItem) {
            $this->line("- {$agendaItem->title} on {$agendaItem->start_date->format('d-m-Y H:i')}");
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run mode: No services were actually created.');
            return 0;
        }

        if (!$this->confirm('Do you want to create services for these agenda items?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $createdCount = 0;
        foreach ($agendaItemsWithoutServices as $agendaItem) {
            Service::create([
                'agenda_item_id' => $agendaItem->id,
                'pastor' => '', // Will be filled in later
                'liturgy' => null,
                'youtube_url' => null,
            ]);
            $createdCount++;
            $this->line("Created service for: {$agendaItem->title}");
        }

        $this->info("Successfully created $createdCount new services.");
        return 0;
    }
}
