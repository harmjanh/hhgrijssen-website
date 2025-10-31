<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomReservation extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'subject',
        'number_of_people',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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
