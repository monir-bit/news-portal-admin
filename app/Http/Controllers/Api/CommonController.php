<?php

namespace App\Http\Controllers\Api;

use App\Applications\Queries\Api\LayoutSectionWiseNewsQuery;
use App\Applications\Queries\Api\RecursiveCategoryQuery;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryTreeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommonController extends Controller
{
    public function __construct(
        protected RecursiveCategoryQuery $recursiveCategoryQuery,
    ) {}

    public function common()
    {
        return Cache::remember('api:common:v2', 60, function () {
            return [
                'site_info' => [
                    'name' => 'আগামীর সময়',
                    'description' => 'আগামীর সময় একটি অনলাইন নিউজ পোর্টাল...',
                ],
                'categories' => CategoryTreeResource::collection(
                    $this->recursiveCategoryQuery->handle()
                ),
            ];
        });
    }



}
