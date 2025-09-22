<?php

namespace App\Console\Commands;

use App\Jobs\DownloadYouTubeVideo;
use App\Models\YouTubeVideo;
use Illuminate\Console\Command;

class DownloadYouTubeVideosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:download
                            {--limit=10 : Maximum number of videos to download}
                            {--not-downloaded : Only download videos that haven\'t been downloaded yet}
                            {--failed : Retry failed downloads}
                            {--video-id= : Download specific video by YouTube ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download YouTube videos and convert them to MP3 files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $notDownloaded = $this->option('not-downloaded');
        $failed = $this->option('failed');
        $videoId = $this->option('video-id');

        if ($videoId) {
            $this->downloadSpecificVideo($videoId);
            return 0;
        }

        $query = YouTubeVideo::query();

        if ($notDownloaded) {
            $query->notDownloaded();
        }

        if ($failed) {
            $query->downloadFailed();
        }

        $videos = $query->limit($limit)->get();

        if ($videos->isEmpty()) {
            $this->info('No videos found to download.');
            return 0;
        }

        $this->info("Found {$videos->count()} videos to download.");

        $progressBar = $this->output->createProgressBar($videos->count());
        $progressBar->start();

        foreach ($videos as $video) {
            DownloadYouTubeVideo::dispatch($video);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Dispatched {$videos->count()} download jobs to the queue.");
        $this->info("Run 'php artisan queue:work' to process the downloads.");

        return 0;
    }

    /**
     * Download a specific video by YouTube ID
     */
    private function downloadSpecificVideo(string $videoId): void
    {
        $video = YouTubeVideo::where('youtube_id', $videoId)->first();

        if (!$video) {
            $this->error("Video with ID '{$videoId}' not found.");
            return;
        }

        $this->info("Dispatching download job for video: {$video->title}");
        DownloadYouTubeVideo::dispatch($video);
        $this->info("Download job dispatched successfully.");
    }
}
