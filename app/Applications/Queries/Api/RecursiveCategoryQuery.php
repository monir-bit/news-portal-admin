<?php

namespace App\Applications\Queries\Api;

use App\Models\Category;

class RecursiveCategoryQuery
{
    public function handle()
    {
        return Category::query()
            ->whereNull('parent_id')
            ->where('visible', true)
            ->orderBy('position')
            ->with([
                'childrenRecursive' => function ($q) {
                    $q->where('visible', true)
                        ->orderBy('position')
                        ->select('id', 'parent_id', 'name', 'slug');
                }
            ])
            ->select('id', 'parent_id', 'name', 'slug')
            ->get();
    }
}
