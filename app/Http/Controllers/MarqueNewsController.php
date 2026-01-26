<?php

namespace App\Http\Controllers;

use App\Models\LiveNews;
use App\Models\MarqueNews;
use App\Services\NewsMarqueeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarqueNewsController extends Controller
{
    public function index()
    {
        $marqueNews = MarqueNews::with('news')->orderBy('position')->get();
        return Inertia::render('marque-news/index', compact('marqueNews'));
    }

    public function delete($id, NewsMarqueeService $service)
    {
        try {
            $liveNews = MarqueNews::with('news')->findOrFail($id);
            $service->disable($liveNews->news);
            return $this->redirectBackWithSuccess('Marque News has been deleted successfully.');
        }
        catch (\Exception $exception) {
            return $this->redirectBackWithError('An error occurred while deleting Marque News: ' . $exception->getMessage());
        }
    }
}
