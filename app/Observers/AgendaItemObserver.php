<?php

namespace App\Observers;

use App\Models\AgendaItem;
use App\Models\Service;
use App\Services\ServiceTitleService;

class AgendaItemObserver
{
    /**
     * Handle the AgendaItem "created" event.
     */
    public function created(AgendaItem $agendaItem): void
    {
        if (ServiceTitleService::shouldHaveService($agendaItem->title)) {
            Service::create([
                'agenda_item_id' => $agendaItem->id,
                'pastor' => '', // Will be filled in later
                'liturgy' => null,
                'youtube_url' => null,
            ]);
        }
    }

    /**
     * Handle the AgendaItem "updated" event.
     */
    public function updated(AgendaItem $agendaItem): void
    {
        // Check if the title was changed to a service title
        if ($agendaItem->wasChanged('title') && ServiceTitleService::shouldHaveService($agendaItem->title)) {
            // Only create a service if one doesn't already exist
            if (!$agendaItem->service) {
                Service::create([
                    'agenda_item_id' => $agendaItem->id,
                    'pastor' => '', // Will be filled in later
                    'liturgy' => null,
                    'youtube_url' => null,
                ]);
            }
        }
    }

    /**
     * Handle the AgendaItem "deleted" event.
     */
    public function deleted(AgendaItem $agendaItem): void
    {
        // Delete the associated service if it exists
        $agendaItem->service?->delete();
    }

    /**
     * Handle the AgendaItem "restored" event.
     */
    public function restored(AgendaItem $agendaItem): void
    {
        //
    }

    /**
     * Handle the AgendaItem "force deleted" event.
     */
    public function forceDeleted(AgendaItem $agendaItem): void
    {
        // Delete the associated service if it exists
        $agendaItem->service?->delete();
    }
}
