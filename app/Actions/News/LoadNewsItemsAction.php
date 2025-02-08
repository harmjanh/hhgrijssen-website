<?php

namespace App\Actions\News;

use App\Models\News;
use App\Models\Setting;

class LoadNewsItemsAction
{
    public function execute()
    {
        $numberOfNewsItems = Setting::getSetting('homepage_number_articles');

        return News::visible()->orderBy('created_at', 'desc')->take($numberOfNewsItems)->get();
    }
}
