<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsTimelineStoreRequest;
use App\Http\Requests\NewsTimelineUpdateRequest;
use App\Models\News;
use App\Models\NewsTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class NewsTimelineController extends Controller
{
    public function index($news_id)
    {
        $news = News::with('timelineNews')->findOrFail($news_id);
        $timelineNews = $news->timelineNews()->orderBy('date', 'desc')->get();
        return Inertia::render('news-timeline/index', compact('news', 'timelineNews'));
    }

    public function store(NewsTimelineStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                NewsTimeline::create($request->validated());
            });
            return redirect()->back()->with('success', 'Timeline news created successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function update(NewsTimelineUpdateRequest $request, $news_id, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $timeline = NewsTimeline::findOrFail($id);
                $timeline->update($request->validated());
            });
            return redirect()->back()->with('success', 'Timeline news updated successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function delete($news_id, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                NewsTimeline::findOrFail($id)->delete();
            });
            return redirect()->back()->with('success', 'Timeline news deleted successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
