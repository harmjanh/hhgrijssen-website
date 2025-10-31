<?php

namespace App\Console\Commands;

use App\Models\DeclarationAttachment;
use App\Services\ImageToPdfService;
use Illuminate\Console\Command;

class ConvertDeclarationImagesToPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'declarations:convert-images-to-pdf
                            {--declaration= : Convert images for specific declaration ID}
                            {--force : Force conversion even if already converted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert declaration image attachments to PDF';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $declarationId = $this->option('declaration');
        $force = $this->option('force');

        $query = DeclarationAttachment::query()
            ->where('mime_type', 'like', 'image/%');

        if ($declarationId) {
            $query->where('declaration_id', $declarationId);
        }

        if (!$force) {
            $query->where('is_pdf_converted', false);
        }

        $attachments = $query->get();

        if ($attachments->isEmpty()) {
            $this->info('No image attachments found to convert.');
            return 0;
        }

        $this->info("Found {$attachments->count()} image attachments to convert.");

        $pdfService = app(ImageToPdfService::class);
        $convertedCount = 0;
        $failedCount = 0;

        $progressBar = $this->output->createProgressBar($attachments->count());
        $progressBar->start();

        foreach ($attachments as $attachment) {
            try {
                if ($pdfService->convertImageToPdf($attachment)) {
                    $convertedCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to convert attachment {$attachment->id}: " . $e->getMessage());
                $failedCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Conversion completed!");
        $this->info("Successfully converted: {$convertedCount}");
        $this->info("Failed conversions: {$failedCount}");

        return 0;
    }
}

