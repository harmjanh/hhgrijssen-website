<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Page extends Model
{
    protected $fillable = ['title', 'content', 'slug', 'page_type_id', 'parent_id', 'sort_order', 'is_active', 'header_image', 'exclude_from_navigation', 'requires_authentication'];

    public function pageType()
    {
        return $this->belongsTo(PageType::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->whereHas('type', function ($query) use ($type) {
            $query->where('name', $type);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function menuable(): MorphOne
    {
        return $this->morphOne(Menu::class, 'menuable');
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }
}
