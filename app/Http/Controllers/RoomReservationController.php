<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomReservationRequest;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\User;
use App\Notifications\KerkzaalReservationCreated;
use App\Notifications\ReservationCancelled;
use App\Notifications\ReservationWithinWeek;
use App\Notifications\ReservationReminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;


class RoomReservationController extends Controller
{
    /**
     * Display a listing of the user's upcoming room reservations.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $reservations = $user->roomReservations()
            ->with('room')
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get();

        return Inertia::render('RoomReservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Display a listing of the user's historical room reservations.
     */
    public function history(): Response
    {
        $user = Auth::user();
        $reservations = $user->roomReservations()
            ->with('room')
            ->where('start_time', '<', now())
            ->orderBy('start_time', 'desc')
            ->get();

        return Inertia::render('RoomReservations/History', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Show the form for creating a new room reservation.
     */
    public function create(): Response
    {
        $rooms = Room::active()->get();

        return Inertia::render('RoomReservations/Create', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * Store a newly created room reservation in storage.
     */
    public function store(RoomReservationRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $reservation = RoomReservation::create([
            'user_id' => $user->id,
            'room_id' => $data['room_id'],
            'subject' => $data['subject'],
            'number_of_people' => $data['number_of_people'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ]);

        // Notify when reservation is for the kerkzaal (church hall)
        $room = $reservation->room;
        if ($room && strcasecmp($room->name, 'Kerkzaal') === 0) {
            Notification::route('mail', 'hhhazelhorst@hhgrijssen.nl')
                ->notify(new KerkzaalReservationCreated($reservation));
        }

        // Notify koster when start time is within the next 7 days
        if ($reservation->start_time->isAfter(now()) && $reservation->start_time->isBefore(now()->addDays(7))) {
            Notification::route('mail', 'koster@hhgrijssen.nl')
                ->notify(new ReservationWithinWeek($reservation, true));
        }

        return redirect()->route('room-reservations.show', $reservation)
            ->with('success', 'Zaalreservering is succesvol aangemaakt.');
    }

    /**
     * Display the specified room reservation.
     */
    public function show(RoomReservation $roomReservation): Response
    {
        // Ensure user can only view their own reservations
        if ($roomReservation->user_id !== Auth::id()) {
            abort(403);
        }

        $roomReservation->load('room');

        return Inertia::render('RoomReservations/Show', [
            'reservation' => $roomReservation,
        ]);
    }

    /**
     * Show the form for editing the specified room reservation.
     */
    public function edit(RoomReservation $roomReservation): Response
    {
        // Ensure user can only edit their own reservations
        if ($roomReservation->user_id !== Auth::id()) {
            abort(403);
        }

        $rooms = Room::active()->get();
        $roomReservation->load('room');

        return Inertia::render('RoomReservations/Edit', [
            'reservation' => $roomReservation,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Update the specified room reservation in storage.
     */
    public function update(RoomReservationRequest $request, RoomReservation $roomReservation): RedirectResponse
    {
        // Ensure user can only update their own reservations
        if ($roomReservation->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();

        $roomReservation->update($data);

        // Notify koster when start time is within the next 7 days
        $roomReservation->load('room');

        // Notify when reservation is for the kerkzaal (church hall)
        $room = $roomReservation->room;
        if ($room && strcasecmp($room->name, 'Kerkzaal') === 0) {
            Notification::route('mail', 'hhhazelhorst@hhgrijssen.nl')
                ->notify(new KerkzaalReservationCreated($roomReservation));
        }
        if ($roomReservation->start_time->isAfter(now()) && $roomReservation->start_time->isBefore(now()->addDays(7))) {
            Notification::route('mail', 'koster@hhgrijssen.nl')
                ->notify(new ReservationWithinWeek($roomReservation, false));
        }

        return redirect()->route('room-reservations.show', $roomReservation)
            ->with('success', 'Zaalreservering is succesvol bijgewerkt.');
    }

    /**
     * Remove the specified room reservation from storage.
     */
    public function destroy(RoomReservation $roomReservation): RedirectResponse
    {
        // Ensure user can only delete their own reservations
        if ($roomReservation->user_id !== Auth::id()) {
            abort(403);
        }

        // Load relationships before deleting
        $roomReservation->load('user', 'room');

        // Send notification to koster if reservation is within a week (before deleting)
        $this->notifyKosterIfWithinWeek($roomReservation);

        // Delete the reservation
        $roomReservation->delete();

        return redirect()->route('room-reservations.index')
            ->with('success', 'Zaalreservering is succesvol verwijderd.');
    }

    /**
     * Get available rooms for a specific time slot.
     */
    public function getAvailableRooms(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        $startTime = $request->start_time;
        $endTime = $request->end_time;

        // Get all active rooms
        $allRooms = Room::active()->get();

        // Filter out rooms that have time conflicts
        $availableRooms = $allRooms->reject(function ($room) use ($startTime, $endTime) {
            return RoomReservation::hasTimeConflict($room->id, $startTime, $endTime);
        });

        return response()->json([
            'available_rooms' => $availableRooms,
        ]);
    }

    /**
     * Cancel a room reservation via signed URL.
     */
    public function cancel(Request $request, RoomReservation $roomReservation): RedirectResponse
    {
        // Verify the signed URL (no login required - the signed link is sufficient)
        if (!$request->hasValidSignature()) {
            abort(403, 'Ongeldige of verlopen link.');
        }

        // Load the user relationship
        $roomReservation->load('user', 'room');

        // Send notification to koster if reservation is within a week (before deleting)
        $this->notifyKosterIfWithinWeek($roomReservation);

        // Delete the reservation
        $roomReservation->delete();

        // Redirect to homepage if user is not authenticated, otherwise to reservations index
        if (Auth::check()) {
            return redirect()->route('room-reservations.index')
                ->with('success', 'Uw zaalreservering is succesvol geannuleerd.');
        }

        return redirect()->route('home')
            ->with('success', 'Uw zaalreservering is succesvol geannuleerd.');
    }

    /**
     * Send notification to koster if the reservation is within a week from now.
     */
    private function notifyKosterIfWithinWeek(RoomReservation $roomReservation): void
    {
        // Check if reservation is within a week from now
        $isWithinWeek = $roomReservation->start_time->isAfter(now())
            && $roomReservation->start_time->isBefore(now()->addWeek());

        if (!$isWithinWeek) {
            return;
        }

        // Extract data for notification
        $userName = $roomReservation->user ? $roomReservation->user->name : 'Onbekend';
        $roomName = $roomReservation->room ? $roomReservation->room->name : 'Onbekend';
        $subject = $roomReservation->subject;
        $numberOfPeople = $roomReservation->number_of_people;
        $startTime = $roomReservation->start_time->toDateTimeString();
        $endTime = $roomReservation->end_time->toDateTimeString();

        // Send notification to koster
        $koster = User::where('email', 'koster@hhgrijssen.nl')->first();
        if ($koster) {
            $koster->notify(new ReservationCancelled(
                $userName,
                $roomName,
                $subject,
                $numberOfPeople,
                $startTime,
                $endTime
            ));
        }
    }
}
