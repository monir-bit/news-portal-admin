<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // 🔹 Main Categories
        $categories = [
            [
                'id' => 1,
                'name' => 'জাতীয়',
                'slug' => 'national',
                'position' => 1,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'রাজনীতি',
                'slug' => 'politics',
                'position' => 2,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'অর্থনীতি',
                'slug' => 'economy',
                'position' => 3,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'আন্তর্জাতিক',
                'slug' => 'international',
                'position' => 4,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'খেলা',
                'slug' => 'sports',
                'position' => 5,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'বিনোদন',
                'slug' => 'entertainment',
                'position' => 6,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'প্রযুক্তি',
                'slug' => 'technology',
                'position' => 7,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'শিক্ষা',
                'slug' => 'education',
                'position' => 8,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'name' => 'স্বাস্থ্য',
                'slug' => 'health',
                'position' => 9,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'name' => 'মতামত',
                'slug' => 'opinion',
                'position' => 10,
                'visible' => true,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Category::insert($categories);
    }
}
