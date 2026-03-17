<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomReservation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'room_id',
        'subject',
        'number_of_people',
        'start_time',
        'end_time',
        'coffee_needed',
        'has_break',
        'beamer_needed',
        'guest_speaker',
        'broadcast_needed',
        'other_remarks',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'coffee_needed' => 'boolean',
        'has_break' => 'boolean',
        'beamer_needed' => 'boolean',
        'guest_speaker' => 'boolean',
        'broadcast_needed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Check if this reservation conflicts with existing reservations
     * including a 30-minute buffer between reservations
     */
    public static function hasTimeConflict($roomId, $startTime, $endTime, $excludeId = null)
    {
        $bufferMinutes = 30;
        $startTime = $startTime instanceof Carbon ? $startTime : Carbon::parse($startTime);
        $endTime = $endTime instanceof Carbon ? $endTime : Carbon::parse($endTime);

        // Add buffer time to the start and end times
        $startWithBuffer = $startTime->copy()->subMinutes($bufferMinutes);
        $endWithBuffer = $endTime->copy()->addMinutes($bufferMinutes);

        $query = static::where('room_id', $roomId)
            ->where(function ($q) use ($startWithBuffer, $endWithBuffer) {
                $q->whereBetween('start_time', [$startWithBuffer, $endWithBuffer])
                  ->orWhereBetween('end_time', [$startWithBuffer, $endWithBuffer])
                  ->orWhere(function ($q2) use ($startWithBuffer, $endWithBuffer) {
                      $q2->where('start_time', '<=', $startWithBuffer)
                         ->where('end_time', '>=', $endWithBuffer);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
