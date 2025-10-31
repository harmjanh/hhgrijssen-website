<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomReservationRequest;
use App\Models\Room;
use App\Models\RoomReservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RoomReservationController extends Controller
{
    /**
     * Display a listing of the user's room reservations.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $reservations = $user->roomReservations()
            ->with('room')
            ->latest('start_time')
            ->get();

        return Inertia::render('RoomReservations/Index', [
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
}
