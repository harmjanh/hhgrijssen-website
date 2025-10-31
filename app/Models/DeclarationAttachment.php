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
        'pdf_path',
        'is_pdf_converted',
        'pdf_converted_at',
    ];

    protected $casts = [
        'is_pdf_converted' => 'boolean',
        'pdf_converted_at' => 'datetime',
    ];

    /**
     * Get the declaration that owns the attachment.
     */
    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Declaration::class);
    }

    /**
     * Check if this attachment is an image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if PDF conversion is available
     */
    public function hasPdfConversion(): bool
    {
        return $this->is_pdf_converted && !empty($this->pdf_path);
    }

    /**
     * Get the full path to the PDF file
     */
    public function getPdfFilePath(): ?string
    {
        if (!$this->hasPdfConversion()) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->path($this->pdf_path);
    }
}
