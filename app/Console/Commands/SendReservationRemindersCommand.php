<?php

namespace App\Console\Commands;

use App\Models\RoomReservation;
use App\Notifications\ReservationReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReservationRemindersCommand extends Command
{
    protected $signature = 'reservations:send-reminders';

    protected $description = 'Send reminder notifications for reservations in 2 days';

    public function handle()
    {
        // Calculate the date range for reservations in 2 days
        $targetDate = Carbon::now()->addDays(2)->startOfDay();
        $endOfDay = $targetDate->copy()->endOfDay();

        $this->info("Checking for reservations on {$targetDate->format('d-m-Y')}...");

        // Find all reservations that start on the target date (in 2 days)
        $reservations = RoomReservation::whereBetween('start_time', [$targetDate, $endOfDay])
            ->with('user', 'room')
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('No reservations found for the target date.');
            return 0;
        }

        $sentCount = 0;
        foreach ($reservations as $reservation) {
            if ($reservation->user && $reservation->user->email) {
                $reservation->user->notify(new ReservationReminder($reservation));
                $sentCount++;
                $this->info("Sent reminder to {$reservation->user->email} for reservation #{$reservation->id}");
            } else {
                $this->warn("Skipping reservation #{$reservation->id} - user or email not found");
            }
        }

        $this->info("Sent {$sentCount} reminder notification(s).");

        return 0;
    }
}

