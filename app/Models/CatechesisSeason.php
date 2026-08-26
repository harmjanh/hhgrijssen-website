<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatechesisSeason extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'is_open',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_open' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (CatechesisSeason $season): void {
            if (! $season->is_open) {
                return;
            }

            static::query()
                ->when($season->exists, fn ($query) => $query->whereKeyNot($season->getKey()))
                ->update(['is_open' => false]);
        });
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CatechesisRegistration::class, 'season_id');
    }

    public static function current(): ?self
    {
        return static::query()->where('is_open', true)->first();
    }
}
