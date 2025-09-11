<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YouTubeVideo extends Model
{
    protected $guarded = [];

    protected $table = 'youtube_videos';

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_hidden', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }
}


