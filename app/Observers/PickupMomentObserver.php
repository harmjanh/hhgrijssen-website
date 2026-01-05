<?php

namespace App\Observers;

use App\Models\Agenda;
use App\Models\AgendaItem;
use App\Models\PickupMoment;
use Carbon\Carbon;

class PickupMomentObserver
{
    /**
     * Handle the PickupMoment "created" event.
     */
    public function created(PickupMoment $pickupMoment): void
    {
        $this->syncAgendaItem($pickupMoment);
    }

    /**
     * Handle the PickupMoment "updated" event.
     */
    public function updated(PickupMoment $pickupMoment): void
    {
        // Only sync if date or active status changed
        if ($pickupMoment->wasChanged('date') || $pickupMoment->wasChanged('active')) {
            $this->syncAgendaItem($pickupMoment);
        }
    }

    /**
     * Handle the PickupMoment "deleted" event.
     */
    public function deleted(PickupMoment $pickupMoment): void
    {
        // Delete associated agenda item
        AgendaItem::where('uid', 'pickup-moment-' . $pickupMoment->id)->delete();
    }

    /**
     * Sync agenda item for pickup moment.
     */
    protected function syncAgendaItem(PickupMoment $pickupMoment): void
    {
        // Get or create the "Collectemunten" agenda
        $agenda = Agenda::firstOrCreate(
            ['title' => 'Collectemunten']
        );

        // Only create/update agenda item if pickup moment is active
        if (!$pickupMoment->active) {
            // Delete agenda item if pickup moment is inactive
            AgendaItem::where('uid', 'pickup-moment-' . $pickupMoment->id)->delete();
            return;
        }

        // Create start date at 10:00 on the pickup date
        $startDate = Carbon::parse($pickupMoment->date)->setTime(10, 0);
        // End date is 1 hour later
        $endDate = $startDate->copy()->addHour();

        // Create or update agenda item
        AgendaItem::updateOrCreate(
            [
                'uid' => 'pickup-moment-' . $pickupMoment->id,
            ],
            [
                'agenda_id' => $agenda->id,
                'title' => 'Afhaalmoment collectemunten',
                'description' => 'Afhaalmoment voor bestelde collectemunten',
                'location' => 'Westerkerk, Haarstraat 95, Rijssen',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        );
    }
}
