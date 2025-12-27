<?php

namespace App\Console\Commands;

use App\Models\RoomReservation;
use App\Models\User;
use App\Notifications\WeeklyReservationsOverview;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

        // Find the koster
        $koster = User::where('email', 'koster@hhgrijssen.nl')->first();

        if (!$koster) {
            $this->error('Koster user not found (koster@hhgrijssen.nl)');
            return 1;
        }

        if ($reservations->isEmpty()) {
            $this->info('No reservations found for the upcoming week.');
            // Still send notification to inform koster there are no reservations
            $koster->notify(new WeeklyReservationsOverview($reservations, $startOfWeek, $endOfWeek));
            return 0;
        }

        $this->info("Found {$reservations->count()} reservation(s) for the upcoming week.");

        // Send notification to koster
        $koster->notify(new WeeklyReservationsOverview($reservations, $startOfWeek, $endOfWeek));

        $this->info("Sent weekly overview to {$koster->email}");

        return 0;
    }
}

