<?php

namespace App\Services;

class ServiceTitleService
{
    /**
     * Get the list of agenda item titles that should have services
     */
    public static function getServiceTitles(): array
    {
        return [
            'Zondagse Dienst',
            'Middagdienst',
            'Avonddienst',
            'Gebedsdienst',
            'Jeugddienst',
        ];
    }

    /**
     * Check if an agenda item title should have a service
     */
    public static function shouldHaveService(string $title): bool
    {
        return in_array($title, self::getServiceTitles());
    }

    /**
     * Get a formatted list of service titles for display
     */
    public static function getServiceTitlesForDisplay(): array
    {
        return array_combine(
            self::getServiceTitles(),
            self::getServiceTitles()
        );
    }
}
