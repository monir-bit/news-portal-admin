<?php

namespace App\Observers;

use App\Applications\Helpers\UtilsHelper;
use App\Models\News;
use App\Services\LiveNewsService;
use App\Services\NewsLatestService;
use App\Services\NewsMarqueeService;

class NewsObserver
{
    /**
     * Handle the News "creating" event.
     */
    public function creating(News $news): void
    {
        $news->slug_key = UtilsHelper::generateUniqueNewsSlugKey();
    }
    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
        app(NewsLatestService::class)->sync($news);
        app(NewsMarqueeService::class)->sync($news);
        app(LiveNewsService::class)->sync($news);
    }

    /**
     * Handle the News "updated" event.
     */
    public function updated(News $news): void
    {
        app(NewsLatestService::class)->sync($news);
        app(NewsMarqueeService::class)->sync($news);
        app(LiveNewsService::class)->sync($news);
    }

    /**
     * Handle the News "deleted" event.
     */
    public function deleted(News $news): void
    {
        app(NewsLatestService::class)->remove($news);
        app(NewsMarqueeService::class)->remove($news);
        app(LiveNewsService::class)->remove($news);
    }

    /**
     * Handle the News "restored" event.
     */
    public function restored(News $news): void
    {
        //
    }

    /**
     * Handle the News "force deleted" event.
     */
    public function forceDeleted(News $news): void
    {
        //
    }
}
