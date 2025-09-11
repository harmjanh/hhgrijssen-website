# YouTube Video Sync System

This system automatically syncs YouTube videos from your channel to the database, allowing you to display them on your website.

## Features

- ✅ Automatic daily sync of YouTube videos
- ✅ Uses official Google API Client Library (latest version)
- ✅ Stores YouTube ID, URL, title, description, and thumbnail
- ✅ Tracks video status (hidden/published)
- ✅ Admin interface for managing videos
- ✅ Frontend display of videos
- ✅ Manual sync command
- ✅ Scheduled daily sync at 6 AM

## Setup Instructions

### 1. Environment Configuration

Add the following variables to your `.env` file:

```env
YOUTUBE_API_KEY=your_youtube_api_key_here
YOUTUBE_CHANNEL_ID=your_channel_id_here
```

### 2. Get YouTube API Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the YouTube Data API v3
4. Create credentials (API Key)
5. Copy the API key to your `.env` file

**Note**: The system now uses the official Google API Client Library, which is more reliable and up-to-date than third-party packages.

### 3. Get Your Channel ID

1. Go to your YouTube channel
2. The channel ID is in the URL: `https://www.youtube.com/channel/CHANNEL_ID`
3. Copy the channel ID to your `.env` file

### 4. Run Migration

The migration should have already been run, but if not:

```bash
php artisan migrate
```

### 5. Test the Sync

Run the sync command manually to test:

```bash
php artisan youtube:sync
```

Or with custom parameters:

```bash
php artisan youtube:sync --channel-id=YOUR_CHANNEL_ID --max-results=25
```

## Usage

### Manual Sync

```bash
# Sync with default settings
php artisan youtube:sync

# Sync with custom channel ID
php artisan youtube:sync --channel-id=UC123456789

# Sync with custom number of videos
php artisan youtube:sync --max-results=100
```

### Admin Interface

1. Go to your Filament admin panel
2. Navigate to "Content" → "YouTube Videos"
3. View, edit, or manually sync videos
4. Use the "Sync from YouTube" button for manual sync

### Frontend

- Videos are available at `/videos`
- Individual videos at `/videos/{id}`
- Only visible and published videos are shown

### Scheduled Sync

The system automatically syncs videos daily at 6 AM. This is configured in `routes/console.php`.

## Database Schema

The `youtube_videos` table contains:

- `youtube_id` - YouTube video ID (unique)
- `url` - Full YouTube URL
- `title` - Video title
- `description` - Video description
- `thumbnail_url` - Video thumbnail URL
- `published_at` - When the video was published on YouTube
- `is_hidden` - Whether the video is hidden/private
- `is_published` - Whether the video is published/public
- `created_at` / `updated_at` - Timestamps

## Troubleshooting

### Common Issues

1. **API Key Error**: Make sure your YouTube API key is valid and has the YouTube Data API v3 enabled
2. **Channel ID Error**: Verify your channel ID is correct
3. **No Videos Found**: Check if your channel has videos and they're not all private
4. **Rate Limiting**: The YouTube API has quotas. If you hit limits, wait and try again
5. **Invalid Page Token Error**: Fixed by using the official Google API Client Library instead of outdated third-party packages
6. **Unknown Part Error**: Fixed by using the official Google API Client Library which properly handles API parts and parameters
7. **Library Compatibility**: Upgraded from `alaouy/youtube` (2020) to `google/apiclient` (latest) for better reliability and support

### Debug Commands

```bash
# Check if the command works
php artisan youtube:sync --help

# Run with verbose output
php artisan youtube:sync -v

# Check scheduled tasks
php artisan schedule:list
```

### Logs

Check Laravel logs for errors:

```bash
tail -f storage/logs/laravel.log
```

## Customization

### Modify Sync Schedule

Edit `routes/console.php` to change when the sync runs:

```php
// Run every 6 hours instead of daily
Schedule::command('youtube:sync')
    ->everyFourHours()
    ->withoutOverlapping()
    ->runInBackground();
```

### Add More Video Fields

1. Create a migration to add new fields
2. Update the `YouTubeService::formatVideoData()` method
3. Update the Filament resource form

### Custom Video Display

Modify the Vue components in `resources/js/Components/YouTubeVideoCard.vue` and `resources/js/Pages/YouTubeVideos/` to customize the frontend display.

## Security Notes

- Keep your YouTube API key secure
- The API key should have minimal required permissions
- Consider using environment-specific API keys for production
- Monitor API usage to avoid hitting quotas

## Support

If you encounter issues:

1. Check the Laravel logs
2. Verify your API key and channel ID
3. Test the sync command manually
4. Check YouTube API quotas and limits
