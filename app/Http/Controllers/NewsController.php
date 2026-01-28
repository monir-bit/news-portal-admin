<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Applications\Enums\MediaActionType;
use App\Applications\Helpers\UtilsHelper;
use App\Applications\Queries\RecursiveCategoryQuery;
use App\Http\Requests\NewsStoreRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Models\Category;
use App\Models\News;
use App\Models\Tag;
use App\Repositories\MediaHelperRepositoryInterface;
use App\Services\LayoutSectionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class NewsController extends Controller
{

    private $media_helper;
    public function __construct(MediaHelperRepositoryInterface $mediaHelper)
    {
        $this->media_helper = $mediaHelper;
    }

    public function index()
    {
        $news = News::with('category')->get();
        return Inertia::render('news/index', compact('news'));
    }

    public function create(RecursiveCategoryQuery $recursiveCategoryQuery, LayoutSectionService $layoutSectionService)
    {
        $tags = Tag::pluck('name');
        $sectionLayouts = $layoutSectionService->getAllSectionLayouts();
        $categories = Category::select('id', 'name')->get();
        return Inertia::render('news/create', compact('tags', 'categories', 'sectionLayouts'));
    }

    public function store(NewsStoreRequest $request, LayoutSectionService $layoutSectionService)
    {
        $data = collect($request->validated());
        $newsData = $data->except(['details', 'tags', 'category_id'])->toArray();
        $newsDetailsData = $data->only(['details'])->toArray();
        $newsTagData = collect($request->tags)->map(fn ($tag) => ['name' => $tag])->toArray();

        DB::beginTransaction();
        try{
            $news = News::create($newsData);
            $this->uploadImage($news, $request->file('image'));

            $news->details()->create($newsDetailsData);
            $news->tags()->createMany($newsTagData);

            $layoutSectionService->updateSectionLayoutNews(
                $news->id,
                $request->section_layout_id,
                $request->section_layout_news_position,
                $request->is_pinned
            );
            DB::commit();
            return $this->redirectBackWithSuccess('News created successfully');
        }
        catch (\Exception $exception){
            DB::rollBack();
            return $this->redirectBackWithError('An error occurred while creating the news: ' . $exception->getMessage());
        }

    }

    public function edit($id, RecursiveCategoryQuery $recursiveCategoryQuery, LayoutSectionService $layoutSectionService)
    {
        $news = News::with(['sectionLayoutNews','details', 'tags'])->findOrFail($id);
        $newsTags = $news->tags->pluck('name');
        $categories = Category::select('id', 'name')->get();
        $tags = Tag::pluck('name');
        $sectionLayouts = $layoutSectionService->getAllSectionLayouts();
        $sectionLayoutNews = $news->sectionLayoutNews;
        $activeSectionLayout = $sectionLayouts->where('id', $sectionLayoutNews?->layout_section_id)->first();
        return Inertia::render('news/edit', compact(
            'news',
            'categories',
            'newsTags',
            'tags',
            'sectionLayouts',
            'sectionLayoutNews',
            'activeSectionLayout'
        ));
    }

    public function update(NewsUpdateRequest $request, $id, LayoutSectionService $layoutSectionService)
    {
        $news = News::with('tags')->findOrFail($id);
        $data = collect($request->validated());
        $newsData = $data->except(['details','image'])->toArray();
        $newsDetailsData = $data->only(['details'])->toArray();
        $newsTagData = collect($request->tags)->map(fn ($tag) => ['name' => $tag])->toArray();
        $newsData['updated_by'] = Auth::id();

        DB::beginTransaction();
        try{

            $news->update($newsData);
            if ($news->details) {
                $news->details->update($newsDetailsData);
            } else {
                $news->details()->create($newsDetailsData);
            }

            $this->uploadImage($news, $request->file('image'), MediaActionType::UPDATE);
            $news->tags()->delete();
            $news->tags()->createMany($newsTagData);
            $layoutSectionService->updateSectionLayoutNews(
                $news->id,
                $request->section_layout_id,
                $request->section_layout_news_position,
                $request->is_pinned
            );
            DB::commit();
            return $this->redirectBackWithSuccess('News updated successfully');
        }
        catch (\Exception $exception){
            DB::rollBack();
            return $this->redirectBackWithError('An error occurred while updating the news: ' . $exception->getMessage());
        }

    }

    public function delete($id)
    {
        News::findOrFail($id)->delete();
        return $this->redirectBackWithSuccess('News deleted successfully');
    }


    public function uploadImage(News $news, UploadedFile | null $uploadedFile , MediaActionType $actionType  = MediaActionType::CREATE)
    {
        if(!$uploadedFile) return false;
        if(!$uploadedFile->isValid()) return false;

        $path = UtilsHelper::MonthYearWisePath();

        if($actionType == 'update' && $news->image){
            $this->media_helper->delete($news->image);
        }

        $path = $this->media_helper->upload($uploadedFile, $path);
        $news->image = $path;
        $news->saveQuietly();
    }

}

