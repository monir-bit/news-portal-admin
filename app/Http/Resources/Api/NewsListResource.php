<?php

namespace App\Http\Resources\Api;

use App\Services\CategoryPathService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug_key,
            'url' => $this->whenLoaded('category', function ($category) {
                return $this->newsUrl($category, $this->slug_key);
            }),
            'title' => $this->title,
            'ticker' => $this->ticker,
            'shoulder' => $this->shoulder,
            'sort_description' => $this->sort_description,
            'live_news' => $this->live_news,
            'is_visible_shoulder' => $this->is_visible_shoulder,
            'is_visible_ticker' => $this->is_visible_ticker,
            'date' => $this->date->toDateTimeString(),
            'category' => $this->whenLoaded('category', function ($category) {
                return [
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }),
        ];
    }


    public function newsUrl($category, $slug): string{
        $path = app(CategoryPathService::class)->build($category);
        return '/'.$path . '/' . $slug;
    }
}
