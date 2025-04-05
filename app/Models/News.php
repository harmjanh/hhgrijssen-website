<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $guarded = [];

    public function scopePublished($query)
    {
        return $query->where('is_published', '=', 1);
    }

    public function scopeVisible($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('visible_from')
                ->orWhere('visible_from', '<=', now());
        })->where(function ($query) {
            $query->whereNull('visible_until')
                ->orWhere('visible_until', '>=', now());
        })->published();
    }
}
