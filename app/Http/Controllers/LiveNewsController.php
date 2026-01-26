<?php

namespace App\Http\Controllers;

use App\Models\LiveNews;
use App\Services\LiveNewsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LiveNewsController extends Controller
{
    public function index()
    {
        $liveNews = LiveNews::with('news')->orderBy('position')->get();
        return Inertia::render('live-news/index', compact('liveNews'));
    }

    public function delete($id, LiveNewsService $service)
    {
        try {
            $liveNews = LiveNews::with('news')->findOrFail($id);
            $service->disable($liveNews->news);
            return $this->redirectBackWithSuccess('Live News deleted successfully.');
        }
        catch (\Exception $exception) {
            return $this->redirectBackWithError('An error occurred while deleting Live News: ' . $exception->getMessage());
        }
    }
}

