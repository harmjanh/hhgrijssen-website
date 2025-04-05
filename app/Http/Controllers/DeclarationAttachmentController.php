<?php

namespace App\Http\Controllers;

use App\Models\Declaration;
use App\Models\DeclarationAttachment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeclarationAttachmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Download a declaration attachment.
     */
    public function download(Declaration $declaration, DeclarationAttachment $attachment): StreamedResponse
    {
        // Check if the user has permission to view this declaration
        $this->authorize('view', $declaration);

        // Check if the attachment belongs to the declaration
        if ($attachment->declaration_id !== $declaration->id) {
            abort(404);
        }

        // Get the file path
        $path = $attachment->path;

        // Check if the file exists
        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        // Return the file as a download
        return response()->streamDownload(function () use ($path) {
            echo Storage::disk('public')->get($path);
        }, $attachment->filename, [
            'Content-Type' => $attachment->mime_type,
        ]);
    }
}
