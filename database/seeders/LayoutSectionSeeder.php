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
                'name' => 'Trending News',
                'slug' => 'trending-news',
                'position' => 1,
                'is_enable' => true,
                'max_news' => 3,
            ],
            [
                'name' => 'Lead News',
                'slug' => 'lead-news',
                'position' => 2,
                'is_enable' => true,
                'max_news' => 5,
            ],
            [
                'name' => 'Sub Lead News',
                'slug' => 'sub-lead-news',
                'position' => 3,
                'is_enable' => true,
                'max_news' => 9,
            ]
        ];

        DB::table('layout_sections')->truncate();
        DB::table('layout_sections')->insert($layoutSections);
    }
}
