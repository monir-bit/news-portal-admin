<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagStoreRequest;
use App\Http\Requests\TagUpdateRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('created_at', 'desc')->get();
        return Inertia::render('tag/index', compact('tags'));
    }

    public function store(TagStoreRequest $request)
    {
        Tag::create([
            'name' => $request->name,
        ]);

        return $this->redirectBackWithSuccess('Tag created successfully');
    }

    public function update(TagUpdateRequest $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update([
            'name' => $request->name,
        ]);

        return $this->redirectBackWithSuccess('Tag updated successfully');
    }

    public function delete($id)
    {
        Tag::findOrFail($id)->delete();
        return $this->redirectBackWithSuccess('Tag deleted successfully');
    }
}

