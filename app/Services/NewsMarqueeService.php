<?php

namespace App\Services;

use App\Models\LatestNews;
use App\Models\MarqueNews;
use App\Models\News;
use Illuminate\Support\Facades\DB;

class NewsMarqueeService
{
    public function sync(News $news): void
    {
        try {
            DB::transaction(function () use ($news) {
                if ($news->news_marquee) {
                    // Check if already exists
                    $existingMarquee = MarqueNews::where('news_id', $news->id)->first();

                    if (!$existingMarquee) {
                        // Get the last position
                        $lastPosition = MarqueNews::max('position') ?? 0;

                        // Create new marquee news with position = last position + 1
                        MarqueNews::create([
                            'news_id' => $news->id,
                            'position' => $lastPosition + 1,
                        ]);
                    }
                } else {
                    // Remove from marquee if news_marquee is false
                    MarqueNews::where('news_id', $news->id)->delete();
                }
            });
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }
    }

    public function remove(News $news): void
    {
        MarqueNews::where('news_id', $news->id)->delete();
    }

    public function disable(News $news): void
    {
        try {
            DB::transaction(function () use ($news) {
                if ($news->news_marquee === true) {
                    $news->news_marquee = false;
                    $news->saveQuietly();
                }
                MarqueNews::where('news_id', $news->id)->delete();
            });
        }
        catch (\Exception $exception){
            info($exception->getMessage());
        }
    }
}

