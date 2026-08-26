<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public const AUDIO_STATUS_QUEUED = 'queued';
    public const AUDIO_STATUS_PROCESSING = 'processing';
    public const AUDIO_STATUS_COMPLETED = 'completed';
    public const AUDIO_STATUS_FAILED = 'failed';

    public const MAX_AUDIO_ATTEMPTS = 3;

    protected $fillable = [
        'agenda_item_id',
        'youtube_video_id',
        'pastor',
        'liturgy',
        'vragen',
        'youtube_url',
        'audio_file_path',
        'audio_status',
        'audio_attempts',
        'audio_error',
        'audio_processed_at',
    ];

    protected $casts = [
        'audio_processed_at' => 'datetime',
    ];

    public function agendaItem()
    {
        return $this->belongsTo(AgendaItem::class);
    }

    public function youtubeVideo()
    {
        return $this->belongsTo(YouTubeVideo::class);
    }

    public function files()
    {
        return $this->hasMany(ServiceFile::class)->orderBy('sort_order');
    }

    public function getStartDateAttribute()
    {
        return $this->agendaItem?->start_date;
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date?->format('d-m-Y H:i');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_date?->format('H:i');
    }

    public function getFormattedStartDateOnlyAttribute()
    {
        return $this->start_date?->format('d-m-Y');
    }

    public function hasAudio(): bool
    {
        return ! empty($this->audio_file_path) || (bool) $this->youtubeVideo?->hasAudio();
    }

    /**
     * Filename for the MP3 on the bucket: contains the service date-time,
     * with the service id as uniqueness guarantee.
     */
    public function getAudioFilename(): string
    {
        return sprintf('dienst-%s-%d.mp3', $this->start_date->format('Y-m-d-Hi'), $this->id);
    }

    /**
     * Services that are eligible for audio processing: started at least
     * 4 hours ago, have a YouTube url, no audio yet (also not via the
     * legacy YouTubeVideo pipeline) and attempts not exhausted.
     */
    public function scopeNeedsAudio(Builder $query): Builder
    {
        return $query
            ->whereHas('agendaItem', function (Builder $q) {
                $q->where('start_date', '<=', now()->subHours(4));
            })
            ->whereNotNull('youtube_url')
            ->where('youtube_url', '!=', '')
            ->whereNull('audio_file_path')
            ->where(function (Builder $q) {
                $q->whereNull('audio_status')
                    ->orWhere(function (Builder $q) {
                        $q->where('audio_status', self::AUDIO_STATUS_FAILED)
                            ->where('audio_attempts', '<', self::MAX_AUDIO_ATTEMPTS);
                    });
            })
            ->whereDoesntHave('youtubeVideo', function (Builder $q) {
                $q->where('download_status', 'completed')
                    ->whereNotNull('audio_file_path');
            });
    }
}
