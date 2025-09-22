<?php

namespace App\Services;

use Google\Client;
use Google\Service\YouTube;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class YouTubeOAuthService
{
    private Client $client;
    private array $config;

    public function __construct()
    {
        $this->config = config('youtube');
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new Client();
        $this->client->setApplicationName($this->config['application_name']);
        $this->client->setClientId($this->config['oauth']['client_id']);
        $this->client->setClientSecret($this->config['oauth']['client_secret']);
        $this->client->setRedirectUri($this->config['oauth']['redirect_uri']);
        $this->client->setScopes($this->config['oauth']['scopes']);
        $this->client->setAccessType($this->config['oauth']['access_type']);
        $this->client->setApprovalPrompt($this->config['oauth']['approval_prompt']);
    }

    /**
     * Get the authorization URL for OAuth 2.0 flow
     */
    public function getAuthorizationUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for access token
     */
    public function exchangeCodeForToken(string $code): array
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                throw new \Exception('Error fetching access token: ' . $token['error']);
            }

            $this->client->setAccessToken($token);
            $this->storeToken($token);

            return $token;
        } catch (\Exception $e) {
            Log::error('Error exchanging code for token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get authenticated YouTube service
     */
    public function getAuthenticatedYouTubeService(): YouTube
    {
        $token = $this->getStoredToken();

        if (!$token) {
            throw new \Exception('No valid token found. Please authenticate first.');
        }

        $this->client->setAccessToken($token);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            $this->refreshToken();
        }

        return new YouTube($this->client);
    }

    /**
     * Check if we have valid credentials
     */
    public function hasValidCredentials(): bool
    {
        $token = $this->getStoredToken();

        if (!$token) {
            return false;
        }

        $this->client->setAccessToken($token);

        return !$this->client->isAccessTokenExpired();
    }

    /**
     * Refresh the access token using refresh token
     */
    public function refreshToken(): void
    {
        $token = $this->getStoredToken();

        if (!$token || !isset($token['refresh_token'])) {
            throw new \Exception('No refresh token available. Please re-authenticate.');
        }

        $this->client->setAccessToken($token);
        $newToken = $this->client->fetchAccessTokenWithRefreshToken($token['refresh_token']);

        if (isset($newToken['error'])) {
            throw new \Exception('Error refreshing token: ' . $newToken['error']);
        }

        $this->storeToken($newToken);
    }

    /**
     * Store token securely
     */
    private function storeToken(array $token): void
    {
        $storageType = $this->config['token_storage']['type'];

        if ($storageType === 'file') {
            $path = $this->config['token_storage']['path'];
            Storage::put($path, json_encode($token));
        } else {
            // For database storage, you would implement this
            // For now, we'll use file storage
            $path = $this->config['token_storage']['path'];
            Storage::put($path, json_encode($token));
        }
    }

    /**
     * Get stored token
     */
    public function getStoredToken(): ?array
    {
        $storageType = $this->config['token_storage']['type'];

        if ($storageType === 'file') {
            $path = $this->config['token_storage']['path'];

            if (!Storage::exists($path)) {
                return null;
            }

            $tokenData = Storage::get($path);
            return json_decode($tokenData, true);
        }

        return null;
    }

    /**
     * Revoke the current token
     */
    public function revokeToken(): void
    {
        $token = $this->getStoredToken();

        if ($token && isset($token['access_token'])) {
            $this->client->revokeToken($token['access_token']);
        }

        // Remove stored token
        $path = $this->config['token_storage']['path'];
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    /**
     * Get the client for manual operations
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
