<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NewsDetailsResource;
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
}
