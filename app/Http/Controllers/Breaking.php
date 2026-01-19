<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BreakingNews;

class Breaking extends Controller
{
    public function index()
    {
        return view('admin.news.breaking.index');
    }

    public function create(Request $request)
    {
        $request->validate([
            'headline' => 'required|max:255',
            'news_url' => 'required'
        ]);
        $data = new BreakingNews();
        $data->headline = $request->headline;
        $data->news_url = $request->news_url;
        $data->status = $request->status;
        $data->save();

        return redirect('/admin/news/breaking/list');
    }

    public function list()
    {
        $breakinglist = BreakingNews::orderBy('id', 'ASC')->get();
        return view('admin.news.breaking.list', compact('breakinglist'));
    }

    public function edit($id)
    {
        $category = BreakingNews::find($id);
        return view('admin.news.breaking.edit', compact('category'));
    }

    public function update(Request $request)
    {
        $data = BreakingNews::find($request->id);
        $data->headline = $request->headline;
        $data->news_url = $request->news_url;
        $data->status = $request->status;
        $data->save();

        return redirect('/admin/news/breaking/list');
    }

    public function delete($id)
    {
        $data = BreakingNews::find($id);
        $data->delete();

        return back();
    }
    
    public function visible($id)
    {
        $category = BreakingNews::find($id);
        $category->status = 1;
        $category->save();
        return back();
    }

    public function invisible($id)
    {
        $category = BreakingNews::find($id);
        $category->status = 0;
        $category->save();
        return back();
    }
}
