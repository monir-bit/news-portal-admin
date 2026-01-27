<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayoutSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layoutSections = [
            [
                'name' => 'Trending Video News',
                'slug' => 'trending-video-news',
                'position' => 1,
                'is_enable' => true,
                'max_news' => 4,
            ],
            [
                'name' => 'Lead News',
                'slug' => 'lead-news',
                'position' => 2,
                'is_enable' => true,
                'max_news' => 4,
            ],
            [
                'name' => 'Pin News',
                'slug' => 'pin-news',
                'position' => 3,
                'is_enable' => true,
                'max_news' => 4,
            ],
            [
                'name' => 'Sub Lead News',
                'slug' => 'sub-lead-news',
                'position' => 4,
                'is_enable' => true,
                'max_news' => 12,
            ]
        ];

        DB::table('layout_sections')->truncate();
        DB::table('layout_sections')->insert($layoutSections);
    }
}
