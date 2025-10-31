<?php

namespace App\Services;

use App\Models\DeclarationAttachment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageToPdfService
{
    /**
     * Convert an image attachment to PDF
     */
    public function convertImageToPdf(DeclarationAttachment $attachment): bool
    {
        try {
            // Check if it's an image file
            if (! $this->isImageFile($attachment->mime_type)) {
                Log::info("Attachment {$attachment->id} is not an image, skipping PDF conversion");

                return false;
            }

            // Check if already converted
            if ($attachment->is_pdf_converted) {
                Log::info("Attachment {$attachment->id} already converted to PDF");

                return true;
            }

            // Get the image path
            $imagePath = Storage::disk('public')->path($attachment->path);

            if (! file_exists($imagePath)) {
                Log::error("Image file not found: {$imagePath}");

                return false;
            }

            // Generate PDF path
            $pdfPath = $this->generatePdfPath($attachment);

            // Create PDF from image
            $this->createPdfFromImage($imagePath, $pdfPath);

            // Update attachment record
            $attachment->update([
                'pdf_path' => $pdfPath,
                'is_pdf_converted' => true,
                'pdf_converted_at' => now(),
            ]);

            Log::info("Successfully converted image to PDF for attachment {$attachment->id}");

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to convert image to PDF for attachment {$attachment->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Check if the file is an image
     */
    private function isImageFile(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    /**
     * Generate PDF path for the attachment
     */
    private function generatePdfPath(DeclarationAttachment $attachment): string
    {
        $originalPath = $attachment->path;
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        return $directory.'/'.$filename.'.pdf';
    }

    /**
     * Create PDF from image using DomPDF
     */
    private function createPdfFromImage(string $imagePath, string $pdfPath): void
    {
        // Get image dimensions
        $imageInfo = getimagesize($imagePath);
        $imageWidth = $imageInfo[0];
        $imageHeight = $imageInfo[1];

        // Convert image to base64 for embedding
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageMimeType = mime_content_type($imagePath);
        $imageBase64 = 'data:'.$imageMimeType.';base64,'.$imageData;

        // Create HTML content with the image
        $html = $this->generateHtmlForImage($imageBase64, $imageWidth, $imageHeight);

        // Generate PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        // Save PDF to storage
        $fullPdfPath = Storage::disk('public')->path($pdfPath);
        $pdf->save($fullPdfPath);
    }

    /**
     * Generate HTML content for the image
     */
    private function generateHtmlForImage(string $imageBase64, int $imageWidth, int $imageHeight): string
    {
        // Calculate appropriate size for A4 page (210mm x 297mm)
        $maxWidth = 190; // mm
        $maxHeight = 280; // mm

        // Calculate scaling factor
        $scaleX = $maxWidth / ($imageWidth * 0.264583); // Convert pixels to mm
        $scaleY = $maxHeight / ($imageHeight * 0.264583);
        $scale = min($scaleX, $scaleY, 1); // Don't scale up

        $scaledWidth = $imageWidth * $scale;
        $scaledHeight = $imageHeight * $scale;

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                body {
                    margin: 0;
                    padding: 10mm;
                    font-family: DejaVu Sans, sans-serif;
                }
                .image-container {
                    text-align: center;
                    page-break-inside: avoid;
                }
                .image-container img {
                    max-width: 100%;
                    height: auto;
                    display: block;
                    margin: 0 auto;
                }
            </style>
        </head>
        <body>
            <div class="image-container">
                <img src="'.$imageBase64.'" alt="Declaration Document" style="width: '.$scaledWidth.'px; height: '.$scaledHeight.'px;" />
            </div>
        </body>
        </html>';
    }

    /**
     * Convert all image attachments for a declaration
     */
    public function convertAllImagesForDeclaration(int $declarationId): int
    {
        $attachments = DeclarationAttachment::where('declaration_id', $declarationId)
            ->where('is_pdf_converted', false)
            ->get();

        $convertedCount = 0;

        foreach ($attachments as $attachment) {
            if ($this->convertImageToPdf($attachment)) {
                $convertedCount++;
            }
        }

        Log::info("Converted {$convertedCount} images to PDF for declaration {$declarationId}");

        return $convertedCount;
    }

    /**
     * Get PDF path for an attachment
     */
    public function getPdfPath(DeclarationAttachment $attachment): ?string
    {
        if (! $attachment->is_pdf_converted || ! $attachment->pdf_path) {
            return null;
        }

        return Storage::disk('public')->path($attachment->pdf_path);
    }

    /**
     * Download PDF for an attachment
     */
    public function downloadPdf(DeclarationAttachment $attachment): ?string
    {
        if (! $attachment->is_pdf_converted || ! $attachment->pdf_path) {
            return null;
        }

        $pdfPath = Storage::disk('public')->path($attachment->pdf_path);

        if (! file_exists($pdfPath)) {
            Log::error("PDF file not found: {$pdfPath}");

            return null;
        }

        return $pdfPath;
    }
}
