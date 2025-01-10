<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Page extends Model
{
    protected $fillable = ['title', 'content', 'slug', 'page_type_id'];

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

    public function menuable(): MorphOne
    {
        return $this->morphOne(Menu::class, 'menuable');
    }
}
