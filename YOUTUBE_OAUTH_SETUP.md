# YouTube OAuth 2.0 Setup Guide

This guide will help you set up OAuth 2.0 authentication for accessing private YouTube videos through the YouTube Data API v3.

## Prerequisites

1. A Google Cloud Platform account
2. A YouTube channel with private videos
3. Laravel application with the Google API Client Library installed

## Step 1: Create Google Cloud Project

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the YouTube Data API v3:
   - Go to "APIs & Services" > "Library"
   - Search for "YouTube Data API v3"
   - Click "Enable"

## Step 2: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth 2.0 Client IDs"
3. Select "Web application" as the application type
4. Configure the OAuth consent screen if prompted:
   - Choose "External" user type
   - Fill in the required fields (App name, User support email, Developer contact)
   - Add your domain to authorized domains
5. Set up the OAuth client:
   - **Name**: Give your OAuth client a descriptive name
   - **Authorized JavaScript origins**: Add your application URL (e.g., `http://localhost:8080`)
   - **Authorized redirect URIs**: Add `http://localhost:8080/youtube/oauth/callback`
6. Click "Create"
7. Copy the **Client ID** and **Client Secret**

## Step 3: Configure Environment Variables

Add the following variables to your `.env` file:

```env
# YouTube API Configuration
YOUTUBE_API_KEY=your_youtube_api_key_here
YOUTUBE_CHANNEL_ID=your_youtube_channel_id_here
YOUTUBE_APPLICATION_NAME="Church Website"

# YouTube OAuth 2.0 Configuration (Required for private videos)
YOUTUBE_OAUTH_CLIENT_ID=your_oauth_client_id_here
YOUTUBE_OAUTH_CLIENT_SECRET=your_oauth_client_secret_here
YOUTUBE_OAUTH_REDIRECT_URI=http://localhost:8080/youtube/oauth/callback
```

## Step 4: Get Your YouTube Channel ID

1. Go to your YouTube channel
2. The channel ID is in the URL: `https://www.youtube.com/channel/YOUR_CHANNEL_ID`
3. Or use the YouTube Data API to find it programmatically

## Step 5: Authenticate and Sync Videos

### Option 1: Command Line Authentication

1. Run the authentication command:
   ```bash
   php artisan youtube:sync --auth
   ```

2. The command will display an authorization URL. Copy and paste it into your browser.

3. Complete the OAuth flow in your browser:
   - Sign in to your Google account
   - Grant permissions to your application
   - Copy the authorization code from the redirect page

4. Paste the authorization code back into the command line.

5. Once authenticated, you can sync videos:
   ```bash
   php artisan youtube:sync
   ```

### Option 2: Web-based Authentication

1. Start your Laravel development server:
   ```bash
   php artisan serve
   ```

2. Visit the authorization URL (you can get this by running `php artisan youtube:sync --auth`)

3. Complete the OAuth flow in your browser

4. The token will be automatically saved

## Step 6: Sync Private Videos

After authentication, you can sync your private YouTube videos:

```bash
# Sync with default settings (processes immediately)
php artisan youtube:sync

# Sync with custom channel ID
php artisan youtube:sync --channel-id=YOUR_CHANNEL_ID

# Sync with custom number of videos
php artisan youtube:sync --max-results=100

# Dispatch jobs to queue for background processing
php artisan youtube:sync --queue

# Combine options
php artisan youtube:sync --channel-id=YOUR_CHANNEL_ID --max-results=500 --queue
```

## Command Options

- `--channel-id`: Specify a different YouTube channel ID
- `--max-results`: Set the maximum number of videos to sync (default: 700)
- `--auth`: Start the OAuth 2.0 authentication flow
- `--queue`: Dispatch jobs to queue instead of processing immediately

## Processing Modes

### Immediate Processing
By default, videos are processed immediately during the sync command. This is suitable for smaller batches.

### Queue Processing
Use the `--queue` flag to dispatch jobs to the queue system for background processing. This is recommended for large batches (500+ videos).

```bash
# Dispatch to queue
php artisan youtube:sync --queue

# Process queued jobs
php artisan queue:work
```

## Memory Efficiency

The system uses PHP generators to process videos efficiently:
- Videos are processed in batches of 50 (YouTube API limit)
- Memory usage remains constant regardless of total video count
- Progress is tracked and displayed during processing

## Troubleshooting

### Common Issues

1. **"OAuth 2.0 authentication is required"**
   - Run `php artisan youtube:sync --auth` to authenticate

2. **"Channel not found"**
   - Verify your channel ID is correct
   - Ensure the channel exists and is accessible

3. **"Invalid redirect URI"**
   - Check that the redirect URI in your OAuth client matches the one in your `.env` file
   - Ensure the URI is added to authorized redirect URIs in Google Cloud Console

4. **"Access token expired"**
   - The system will automatically refresh tokens, but if it fails, re-authenticate with `--auth`

5. **"Insufficient permissions"**
   - Ensure you've granted the necessary scopes during OAuth flow
   - Check that your OAuth consent screen is properly configured

### Token Storage

Tokens are stored securely in `storage/app/youtube-tokens.json`. This file contains:
- Access token
- Refresh token
- Token expiration time
- Scopes granted

**Important**: Keep this file secure and don't commit it to version control.

## Security Considerations

1. **Never commit tokens to version control**
2. **Use HTTPS in production**
3. **Regularly rotate OAuth credentials**
4. **Monitor API usage and quotas**
5. **Implement proper error handling**

## API Quotas

The YouTube Data API v3 has daily quotas:
- 10,000 units per day (default)
- Each API call consumes different amounts of units
- Monitor your usage in the Google Cloud Console

## Support

If you encounter issues:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify your OAuth configuration
3. Test with a simple API call first
4. Check Google Cloud Console for API errors

## Production Deployment

For production:
1. Update redirect URIs to use your production domain
2. Use HTTPS for all OAuth URLs
3. Set up proper error monitoring
4. Consider implementing token refresh logic
5. Use environment-specific OAuth credentials
