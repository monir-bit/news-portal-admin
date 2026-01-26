<?php

namespace App\Services;

use App\Models\LatestNews;
use App\Models\LiveNews;
use App\Models\News;
use Illuminate\Support\Facades\DB;

class NewsLatestService
{
    public function sync(News $news): void
    {
        try {
            DB::transaction(function () use ($news) {
                if ($news->latest) {
                    LatestNews::updateOrCreate(
                        ['news_id' => $news->id],
                        ['position' => LatestNews::max('position') + 1]
                    );
                } else {
                    LatestNews::where('news_id', $news->id)->delete();
                }
            });
        }
        catch (\Exception $exception){
            info($exception->getMessage());
        }
    }


    public function remove(News $news): void
    {
        LatestNews::where('news_id', $news->id)->delete();
    }

    public function disable(News $news): void
    {
        try {
            DB::transaction(function () use ($news) {
                if ($news->latest === true) {
                    $news->latest = false;
                    $news->saveQuietly();
                }
                LatestNews::where('news_id', $news->id)->delete();
            });
        }
        catch (\Exception $exception){
            info($exception->getMessage());
        }
    }
}

