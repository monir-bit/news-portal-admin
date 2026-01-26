<?php

namespace App\Services;

use App\Models\LiveNews;
use App\Models\News;
use Illuminate\Support\Facades\DB;

class LiveNewsService
{
    public function sync(News $news): void
    {
        try {
            DB::transaction(function () use ($news) {
                if ($news->live_news) {
                    LiveNews::updateOrCreate(
                        ['news_id' => $news->id],
                        ['position' => LiveNews::max('position') + 1]
                    );
                } else {
                    LiveNews::where('news_id', $news->id)->delete();
                }
            });
        }
        catch (\Exception $exception){
            info($exception->getMessage());
        }
    }


    public function remove(News $news): void
    {
        LiveNews::where('news_id', $news->id)->delete();
    }

    public function disable(News $news): void
    {
        try {
            DB::transaction(function () use ($news) {
                if ($news->live_news === true) {
                    $news->live_news = false;
                    $news->saveQuietly();
                }
                LiveNews::where('news_id', $news->id)->delete();
            });
        }
        catch (\Exception $exception){
            info($exception->getMessage());
        }
    }

}

