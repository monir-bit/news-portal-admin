<?php

namespace App\Http\Controllers;

use App\Models\LatestNews;
use App\Services\NewsLatestService;
use Inertia\Inertia;

class LatestNewsController extends Controller
{
    public function index()
    {
        $latestNews = LatestNews::with('news')->orderBy('position')->get();
        return Inertia::render('latest-news/index', compact('latestNews'));
    }

    public function delete($id, NewsLatestService $service)
    {
        try {
            $liveNews = LatestNews::with('news')->findOrFail($id);
            $service->disable($liveNews->news);
            return $this->redirectBackWithSuccess('Latest News deleted successfully.');
        }
        catch (\Exception $exception) {
            return $this->redirectBackWithError('An error occurred while deleting Latest News: ' . $exception->getMessage());
        }
    }
}
