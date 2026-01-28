<?php

namespace App\Http\Resources\Api;

use App\Services\CategoryPathService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsDetailsResource extends JsonResource
{
    public static $wrap = null;
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
            'image' => $this->image,
            'shoulder' => $this->shoulder,
            'sort_description' => $this->sort_description,
            'live_news' => $this->live_news,
            'is_visible_shoulder' => $this->is_visible_shoulder,
            'is_visible_ticker' => $this->is_visible_ticker,
            'date' => $this->date->toDateTimeString(),
            'details' => $this->whenLoaded('details', function () {
                return [
                    'description' => $this->details->details,
                    'keyword' => $this->details->keyword,
                    'video_link' => $this->details->video_link,
                    'google_drive_link' => $this->details->google_drive_link,
                    'audio_link' => $this->details->audio_link
                ];
            }),
            'category' => $this->whenLoaded('category', function ($category) {
                return [
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->pluck('name');
            }),
        ];
    }


    public function newsUrl($category, $slug): string{
        $path = app(CategoryPathService::class)->build($category);
        return '/'.$path . '/' . $slug;
    }
}
