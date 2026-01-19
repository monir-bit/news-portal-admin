<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subcategory as SubcategoryModel;
use App\Models\Category as CategoryModel;

class Subcategory extends Controller
{
    public function index()
    {
        $categorylist = CategoryModel::get();
        return view('admin.news.subcategory.index', compact('categorylist'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|max:255'
        ]);
        $subcategory = new SubcategoryModel();
        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;
        
        // Handle slug: if provided, use it directly (after sanitization and uniqueness check)
        if (!empty($request->slug)) {
            // Sanitize the slug (remove spaces, special chars)
            $slug = strtolower(trim($request->slug));
            $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
            $slug = trim($slug, '-');
            
            // Check if slug is unique
            $existing = \App\Models\Subcategory::where('slug', $slug)->first();
            
            if ($existing) {
                // If not unique, append counter
                $counter = 1;
                $originalSlug = $slug;
                while (\App\Models\Subcategory::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
            
            $subcategory->slug = $slug;
        } else {
            // Auto-generate from name if slug is empty
            $subcategory->slug = \App\Models\Subcategory::generateUniqueSlug($request->name, null);
        }
        
        $subcategory->save();

        return redirect('/admin/news/subcategory/list');
    }

    public function list()
    {
        $subcategorylist = SubcategoryModel::join('categories', 'subcategories.category_id', 'categories.id')->select('subcategories.*', 'categories.name as category_name')->get();
        return view('admin.news.subcategory.list', compact('subcategorylist'));
    }

    public function view($id)
    {
        $subcategory = SubcategoryModel::where('id', $id)->first();

        return view('admin.news.subcategory.view', compact('subcategory'));
    }

    public function edit($id)
    {
        $categorylist = CategoryModel::get();
        $subcategory = SubcategoryModel::where('id', $id)->first();
        return view('admin.news.subcategory.edit', compact('subcategory', 'categorylist'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|max:255'
        ]);
        $subcategory = SubcategoryModel::where('id', $request->id)->first();
        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;
        
        // Handle slug: if provided, use it directly (after sanitization and uniqueness check)
        if (!empty($request->slug)) {
            // Sanitize the slug (remove spaces, special chars)
            $slug = strtolower(trim($request->slug));
            $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
            $slug = trim($slug, '-');
            
            // Check if slug is unique (excluding current subcategory)
            $existing = \App\Models\Subcategory::where('slug', $slug)
                ->where('id', '!=', $subcategory->id)
                ->first();
            
            if ($existing) {
                // If not unique, append counter
                $counter = 1;
                $originalSlug = $slug;
                while (\App\Models\Subcategory::where('slug', $slug)->where('id', '!=', $subcategory->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
            
            $subcategory->slug = $slug;
        } else {
            // Auto-generate from name if slug is empty
            $subcategory->slug = \App\Models\Subcategory::generateUniqueSlug($request->name, $subcategory->id);
        }
        
        $subcategory->save();

        return redirect('/admin/news/subcategory/list');
    }

    public function delete($id): \Illuminate\Http\RedirectResponse
    {
        $subcategory = SubcategoryModel::where('id', $id)->first();
        $subcategory->delete();

        return back();
    }

    public function getSubCategory($categoryId): \Illuminate\Http\JsonResponse
    {
        $subCategory = SubcategoryModel::where('category_id', $categoryId)->get();
        return response()->json($subCategory);
    }

    public function getSubCategoryByJson(Request $request)
    {
        $subCategory = SubcategoryModel::whereIn('category_id', json_decode($request->data))->get();
        return response()->json($subCategory);
        // return response()->json($request->data);
    }

    public function visible($id)
    {
        $subCategory = SubcategoryModel::where('id', $id)->first();
        $subCategory->visible = 1;
        $subCategory->save();
        return back();
    }

    public function invisible($id)
    {
        $subCategory = SubcategoryModel::where('id', $id)->first();
        $subCategory->visible = 0;
        $subCategory->save();
        return back();
    }
}
