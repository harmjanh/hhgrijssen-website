<?php

namespace App\Models;

use App\Enums\CatechesisGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatechesisRegistration extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'season_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'group',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'group' => CatechesisGroup::class,
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(CatechesisSeason::class, 'season_id');
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
