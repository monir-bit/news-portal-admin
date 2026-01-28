<?php

namespace App\Http\Controllers\Api;

use App\Applications\Queries\Api\LayoutSectionWiseNewsQuery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{

    public function __construct(protected LayoutSectionWiseNewsQuery $layoutSectionWiseNewsQuery)
    {}

    public function homeInitial() {
//        return Cache::remember('api:home_initial:v1', 60, function () {
            return [
                'trending_video_news' => $this->layoutSectionWiseNewsQuery->handle('trending-video-news'),
                'lead_news' => $this->layoutSectionWiseNewsQuery->handle('lead-news'),
                'pin_news' => $this->layoutSectionWiseNewsQuery->handle('pin-news'),
                'sub_lead_news' => $this->layoutSectionWiseNewsQuery->handle('sub-lead-news'),
            ];
//        });
    }
}
