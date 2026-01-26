<?php

namespace App\Http\Controllers;

use App\Applications\Queries\RecursiveCategoryQuery;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::get();
        return Inertia::render('category/index', compact('categories'));
    }

    public function create(RecursiveCategoryQuery $recursiveCategoryQuery)
    {
        $recursiveCategories = $recursiveCategoryQuery->handle();
        $categories = Category::get(['id', 'name', 'parent_id']);
        return Inertia::render('category/create', compact('categories', 'recursiveCategories'));
    }


    public function store(CategoryStoreRequest $request)
    {
        try {
            Category::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'position' => $request->position ?? 0,
                'visible' => $request->boolean('visible'),
                'parent_id' => $request->parent_id,
            ]);

            return $this->redirectBackWithSuccess('Category created successfully');
        }
        catch (\Exception $e) {
            return $this->redirectBackWithError('An error occurred while creating the category: ' . $e->getMessage());
        }
    }

    public function edit($id, RecursiveCategoryQuery $recursiveCategoryQuery)
    {
        $category = Category::findOrFail($id);
        $recursiveCategories = $recursiveCategoryQuery->handle();
        $categories = Category::get(['id', 'name', 'parent_id']);
        return Inertia::render('category/edit', compact(
            'categories',
            'recursiveCategories',
            'category'
        ));
    }

    public function update(CategoryUpdateRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();
        $category->update($data);
        return $this->redirectBackWithSuccess('Category updated successfully');
    }

    public function delete($id)
    {
        Category::findOrFail($id)->delete();
        return $this->redirectBackWithSuccess('Category deleted successfully');
    }
}


