<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarqueeController extends Controller
{
    public function getNews($date)
    {
        $date = date('Y-m-d');
        $news = News::select('id','title')->where('published', 1)->where('news_marquee',1)->orderBy('id','DESC')->limit(20)->get();

        return response()->json($news);
    }
}