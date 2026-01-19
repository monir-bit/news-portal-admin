<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category as CategoryModel;


class Category extends Controller
{
    public function index()
    {
        return view('admin.news.category.index');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'order' => 'required'
        ]);
        $category = new CategoryModel();
        $category->name = $request->name;
        $category->order = $request->order;
        
        // Handle slug: if provided, use it directly (after sanitization and uniqueness check)
        if (!empty($request->slug)) {
            // Sanitize the slug (remove spaces, special chars)
            $slug = strtolower(trim($request->slug));
            $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
            $slug = trim($slug, '-');
            
            // Check if slug is unique
            $existing = \App\Models\Category::where('slug', $slug)->first();
            
            if ($existing) {
                // If not unique, append counter
                $counter = 1;
                $originalSlug = $slug;
                while (\App\Models\Category::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
            
            $category->slug = $slug;
        } else {
            // Auto-generate from name if slug is empty
            $category->slug = \App\Models\Category::generateUniqueSlug($request->name, null);
        }
        
        $category->save();

        return redirect('/admin/news/category/list');
    }

    public function list()
    {
        $categorylist = CategoryModel::orderBy('order', 'ASC')->get();
        return view('admin.news.category.list', compact('categorylist'));
    }

    public function view($id)
    {
        $category = CategoryModel::where('id', $id)->first();
        return view('admin.news.category.view', compact('category'));
    }

    public function edit($id)
    {
        $category = CategoryModel::where('id', $id)->first();
        return view('admin.news.category.edit', compact('category'));
    }

    public function update(Request $request)
    {
        // if ($request->id < 20) {
        //     return redirect('/admin/news/category/list');
        // }
        $category = CategoryModel::where('id', $request->id)->first();
        $category->name = $request->name;
        $category->order = $request->order;
        
        // Handle slug: if provided, use it directly (after sanitization and uniqueness check)
        if (!empty($request->slug)) {
            // Sanitize the slug (remove spaces, special chars)
            $slug = strtolower(trim($request->slug));
            $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
            $slug = trim($slug, '-');
            
            // Check if slug is unique (excluding current category)
            $existing = \App\Models\Category::where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->first();
            
            if ($existing) {
                // If not unique, append counter
                $counter = 1;
                $originalSlug = $slug;
                while (\App\Models\Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
            
            $category->slug = $slug;
        } else {
            // Auto-generate from name if slug is empty
            $category->slug = \App\Models\Category::generateUniqueSlug($request->name, $category->id);
        }
        
        $category->save();

        return redirect('/admin/news/category/list');
    }

    public function delete($id)
    {
        if ($id < 21) {
            return redirect('/admin/news/category/list');
        }
        $category = CategoryModel::where('id', $id)->first();
        $category->delete();

        return back();
    }

    public function visible($id)
    {
        $category = CategoryModel::where('id', $id)->first();
        $category->visible = 1;
        $category->save();
        return back();
    }

    public function invisible($id)
    {
        $category = CategoryModel::where('id', $id)->first();
        $category->visible = 0;
        $category->save();
        return back();
    }
}
