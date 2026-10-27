<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class News extends Model
{
    protected $guarded = [];

    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected static function booted(): void
    {
        static::creating(function (News $news) {
            if (empty($news->slug)) {
                $news->slug = static::generateUniqueSlug($news->title);
            }
        });

        static::updating(function (News $news) {
            if ($news->isDirty('title') && ! $news->isDirty('slug')) {
                $news->slug = static::generateUniqueSlug($news->title, $news->id);
            }
        });
    }

    public function pageViews(): MorphMany
    {
        return $this->morphMany(PageView::class, 'viewable');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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
