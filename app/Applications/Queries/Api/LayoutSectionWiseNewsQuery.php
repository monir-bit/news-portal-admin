<?php

namespace App\Applications\Queries\Api;

use App\Http\Resources\Api\NewsListResource;
use App\Models\LayoutSection;
use App\Models\LayoutSectionNews;

class LayoutSectionWiseNewsQuery
{
    public function handle(string $section_slug){
        $layout_section = LayoutSection::where('slug', $section_slug)->first();
        if(!$layout_section) {
            return [];
        }

        $news_list = LayoutSectionNews::with('news.category.parentRecursive')
            ->where('layout_section_id', $layout_section->id)
            ->whereHas('news', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->orderBy('position', 'ASC')
            ->get()->map(function ($item) {
                return [
                    'position' => $item->position,
                    'news' => NewsListResource::make($item->news),
                ];
            });


        return $news_list;

    }
}
