<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PageView extends Model
{
    use MassPrunable;

    public const UPDATED_AT = null;

    protected $guarded = [];

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<=', now()->subYear());
    }
}
