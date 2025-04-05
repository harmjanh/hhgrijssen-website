<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeclarationAttachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'declaration_id',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    /**
     * Get the declaration that owns the attachment.
     */
    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Declaration::class);
    }
}
