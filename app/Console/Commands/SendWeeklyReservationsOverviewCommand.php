<?php

namespace App\Console\Commands;

use App\Models\RoomReservation;
use App\Notifications\WeeklyReservationsOverview;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendWeeklyReservationsOverviewCommand extends Command
{
    protected $signature = 'reservations:send-weekly-overview';

    protected $description = 'Send weekly overview of upcoming reservations to the koster';

    public function handle()
    {
        // Calculate the date range for the upcoming week (Monday to Sunday)
        // If today is Saturday, the upcoming week starts next Monday
        $now = Carbon::now();
        $startOfWeek = $now->copy()->next(Carbon::MONDAY)->startOfDay();
        $endOfWeek = $startOfWeek->copy()->addDays(6)->endOfDay(); // Add 6 days to get Sunday

        $this->info("Checking for reservations from {$startOfWeek->format('d-m-Y')} to {$endOfWeek->format('d-m-Y')}...");

        // Find all reservations in the upcoming week
        $reservations = RoomReservation::whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->with('user', 'room')
            ->orderBy('start_time')
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('No reservations found for the upcoming week.');
            // Still send notification to inform koster there are no reservations
            Notification::route('mail', [
                'koster@hhgrijssen.nl',
                'hhhazelhorst@hhgrijssen.nl',
            ])->notify(new WeeklyReservationsOverview($reservations, $startOfWeek, $endOfWeek));
            return 0;
        }

        $this->info("Found {$reservations->count()} reservation(s) for the upcoming week.");

        // Send notification to reservation contacts
        Notification::route('mail', [
            'koster@hhgrijssen.nl',
            'hhhazelhorst@hhgrijssen.nl',
        ])->notify(new WeeklyReservationsOverview($reservations, $startOfWeek, $endOfWeek));

        $this->info('Sent weekly overview to reservation contacts.');

        return 0;
    }
}

