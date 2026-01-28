<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NewsDetailsResource;
use App\Http\Resources\Api\NewsListResource;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function newsDetails(string $slug)
    {
        $news = News::where('slug_key', $slug)
            ->with([
                'category.parentRecursive',
                'details',
                'tags'
            ])->firstOrFail();

        return NewsDetailsResource::make($news);
    }

    public function newsByCategory($slug)
    {
        $category = Category::where('slug', $slug)->where('visible', true)->firstOrFail();
        $query = News::where('category_id', $category->id)
            ->with(['category.parentRecursive'])
            ->orderBy('created_at', 'DESC');

        $leadNews = clone $query->skip(0)->take(5)->get();
        $subLeadNews = clone $query->skip(5)->take(2)->get();
        $othersNews = clone $query->skip(7)->take(12)->get();

        return [
            'category' => [
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'news_list' => [
                'lead_news' => NewsListResource::collection($leadNews),
                'sub_lead_news' => NewsListResource::collection($subLeadNews),
                'others_news' => NewsListResource::collection($othersNews),
            ],
        ];

    }

}
