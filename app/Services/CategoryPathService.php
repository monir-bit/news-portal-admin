<?php

namespace App\Services;

use App\Models\Category;

class CategoryPathService
{
    public function build(Category $category): string
    {
        $segments = [];

        while ($category) {
            $segments[] = $category->slug;
            $category = $category->parent;
        }

        return implode('/', array_reverse($segments));
    }
}
