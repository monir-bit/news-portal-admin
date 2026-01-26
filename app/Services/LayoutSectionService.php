<?php

namespace App\Services;

use App\Models\LayoutSection;
use App\Models\LayoutSectionNews;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LayoutSectionService
{
    public function getAllSectionLayouts(): Collection
    {
        return LayoutSection::where('is_enable', true)->orderBy('position')->select('id', 'slug', 'name', 'max_news')->get()->map(function ($section) {
            return [
                'id' => $section->id,
                'slug' => $section->slug,
                'name' => $section->name,
                'max_news' => $section->max_news,
                'order_sequence' => $this->generateSequence($section->max_news),
            ];
        });
    }


    public function generateSequence(int | null $max_news): array {
        if (!$max_news) return [];
        return range(1,$max_news);
    }


    public function updateSectionLayoutNews(int $newsId, string $section_layout_id, int $position, bool $isPinned = false) {
        return DB::transaction(function () use ($newsId, $section_layout_id, $position, $isPinned) {

            $layoutSection = LayoutSection::where('id', $section_layout_id)
                ->where('is_enable', true)
                ->first();

            if (!$layoutSection) {
                return false;
            }

            // 🔁 shift others (>= target position)
            LayoutSectionNews::where('layout_section_id', $layoutSection->id)
                ->where('position', '>=', $position)
                ->when(!$isPinned, function ($q) {
                    // normal update → everyone shifts
                    return $q;
                }, function ($q) {
                    // pinned → only existing ones shift (same logic, but explicit)
                    return $q;
                })
                ->increment('position');

            // 🔁 insert or update current news
            $sectionLayoutNews = LayoutSectionNews::updateOrCreate(
                [
                    'layout_section_id' => $layoutSection->id,
                    'news_id' => $newsId,
                ],
                [
                    'position' => $position,
                ]
            );

            return $sectionLayoutNews;
        });
    }


}
