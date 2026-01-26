<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function home() {
        return Cache::remember('api:home:v1', 60, function () {
            return [
//                'lead_news' => ,
//                'sub_lead_news' => ,
            ];
        });
    }
}
