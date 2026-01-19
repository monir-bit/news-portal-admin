<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Latest;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsSubCategory;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    /**
     * Normalize image URL - replace breakingnews.com.bd with current domain
     */
    private function normalizeImageUrl($url)
    {
        if (empty($url)) {
            return $url;
        }

        // If URL contains breakingnews.com.bd, replace with current domain
        if (strpos($url, 'agamirsomoy.com') !== false) {
            $currentDomain = request()->getSchemeAndHttpHost();
            $url = str_replace('https://agamirsomoy.com', $currentDomain, $url);
            $url = str_replace('http://agamirsomoy.com', $currentDomain, $url);
        }

        // If URL is relative, make it absolute
        if (!empty($url) && strpos($url, 'http') !== 0) {
            $currentDomain = request()->getSchemeAndHttpHost();
            $url = rtrim($currentDomain, '/') . '/' . ltrim($url, '/');
        }

        return $url;
    }
    public function getCategory(): \Illuminate\Http\JsonResponse
    {
        $categories = Category::where('visible', 1)->with('subCategories')->orderBy('order', 'ASC')->get();
        foreach ($categories as $category) {
            $category->url = $category->route ?? '/' . ($category->slug ?? 'category-' . $category->id);
            if ($category->subCategories) {
                foreach ($category->subCategories as $subCategory) {
                    $categorySlug = $category->slug ?? 'category-' . $category->id;
                    $subcategorySlug = $subCategory->slug ?? 'subcategory-' . $subCategory->id;
                    $subCategory->url = "/{$categorySlug}/{$subcategorySlug}";
                }
            }
        }
        return response()->json([$categories]);
    }

    public function getCategoryById($id)
    {
        $category = Category::where('id', $id)->with('subCategories')->first();
        if ($category) {
            $category->url = $category->route ?? '/' . ($category->slug ?? 'category-' . $category->id);
            if ($category->subCategories) {
                foreach ($category->subCategories as $subCategory) {
                    $categorySlug = $category->slug ?? 'category-' . $category->id;
                    $subcategorySlug = $subCategory->slug ?? 'subcategory-' . $subCategory->id;
                    $subCategory->url = "/{$categorySlug}/{$subcategorySlug}";
                }
            }
        }
        return response()->json($category);
    }

    public function getAllNews($categoryId, $type, $limit, $skip = 0, $sub = 0): \Illuminate\Http\JsonResponse
    {
        $key = 'category_id';
        if ($sub) {
            if ($categoryId == 1) {
                $newsIds = News::where('published', 1)
                    ->join('news_sub_categories', 'news_sub_categories.news_id', 'news.id')
                    ->where('news_sub_categories.sub_category_id', $sub)
                    ->orderBy('news.order', 'asc')
                    ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
                    ->where('type', $type)
                    ->select('news.id')
                    ->skip($skip)
                    ->take($limit)
                    ->get();
            } else {
                $newsIds = News::where('published', 1)
                    ->join('news_sub_categories', 'news_sub_categories.news_id', 'news.id')
                    ->where('news_sub_categories.sub_category_id', $sub)
                    // ->orderBy('news.date','DESC')
                    ->orderBy('news.id', 'desc')
                    ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
                    ->select('news.id')
                    ->skip($skip)
                    ->take($limit)
                    ->get();
            }

            $news = [];
            foreach ($newsIds as $id) {
                $news[] = $this->newsById($id->id);
            }

            return response()->json($news);
        }

        if ($categoryId == 1) {
            $newsIds = News::where('published', 1)->join('news_categories', 'news_categories.news_id', 'news.id')
            ->where('news_categories.category_id', $categoryId)
            // ->orderByRaw("DATE_FORMAT('Y-m-d',news.date), DESC")
            // ->orderBy('news.date','DESC')
            ->orderBy('news.order', 'asc')
            ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
            ->where('type', $type)
            ->select('news.id')
            ->skip($skip)
            ->take($limit)
            ->get();
        }else{
            $newsIds = News::where('published', 1)->join('news_categories', 'news_categories.news_id', 'news.id')
            ->where('news_categories.category_id', $categoryId)
            ->orderBy('news.id', 'desc')
            ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
            ->select('news.id')
            ->skip($skip)
            ->take($limit)
            ->get();
        }


        $news = [];
        foreach ($newsIds as $id) {
            $news[] = $this->newsById($id->id);
        }

        return response()->json($news);
    }

    public function increaseCount($id)
    {
        $latest = Latest::where('news_id', $id)->where('date', Date('Y-m-d'))->first();
        if ($latest) {
            $latest->count += 1;
            $latest->save();
        } else {
            $latest = new Latest();
            $latest->news_id = $id;
            $latest->count = 1;
            $latest->date = Date('Y-m-d');
            $latest->save();
        }

        $news = News::find($id);
        News::where('id', $id)->update(['hit_counter' => $news->hit_counter + 1]);
    }

    public function newsById($id)
    {
        $news = News::find($id);
        $categories = NewsCategory::join('categories', 'categories.id', 'news_categories.category_id')->where('news_categories.news_id', $id)->select('categories.id', 'categories.name', 'categories.slug')->get();
        $subCategories = NewsSubCategory::join('subcategories', 'news_sub_categories.sub_category_id', 'subcategories.id')->where('news_sub_categories.news_id', $id)->select('subcategories.id', 'subcategories.name', 'subcategories.slug')->get();
        $this->increaseCount($id);
        $keyWords = [];
        foreach ($news->keywordList as $item) {
            $keyWords[] = [
                'id' => isset($item->keywordItem->id) ? $item->keywordItem->id : '',
                'name' => isset($item->keywordItem->name) ? $item->keywordItem->name : '',
            ];
        }

        // Get primary category and subcategory for URL generation
        $primaryCategory = $categories->first();
        $primarySubCategory = $subCategories->first();

        // Generate URL (Prothom Alo style)
        $newsUrl = '#';
        if ($primaryCategory) {
            $categorySlug = $primaryCategory->slug ?? 'category-' . $primaryCategory->id;
            if ($primarySubCategory) {
                $subcategorySlug = $primarySubCategory->slug ?? 'subcategory-' . $primarySubCategory->id;
                $newsSlug = $news->slug ?? 'news-' . $news->id;
                $newsUrl = "/{$categorySlug}/{$subcategorySlug}/{$newsSlug}";
            } else {
                $newsSlug = $news->slug ?? 'news-' . $news->id;
                $newsUrl = "/{$categorySlug}/{$newsSlug}";
            }
        }

        return [
            'id' => $news->id,
            'title' => $news->title,
            'caption' => $news->caption,
            'sort_description' => strip_tags($news->sort_description),
            'order' => $news->order,
            'category' => $categories,
            'sub_category' => $subCategories,
            'date' => $news->date,
            'image' => $this->normalizeImageUrl($news->image),
            'type' => $news->type,
            'details' => $news->details->details,
            'ticker' => $news->details->ticker,
            'shoulder' => $news->details->shoulder,
            'representative' => $news->reporter ? $news->reporter->name : $news->details->representative,
            'representative_image'    =>  $this->normalizeImageUrl($news->reporter ? $news->reporter->image : asset('img/logo2.png')),
            'video_link' => $news->details->video_link,
            'google_drive_link' => $news->details->google_drive_link,
            'audio_link' => $news->details->audio_link,
            'keyword' => $keyWords,
            'timeline_id' => $news->timeline_id,
            'slug' => $news->slug ?? 'news-' . $news->id,
            'category_slug' => $primaryCategory ? ($primaryCategory->slug ?? 'category-' . $primaryCategory->id) : null,
            'subcategory_slug' => $primarySubCategory ? ($primarySubCategory->slug ?? 'subcategory-' . $primarySubCategory->id) : null,
            'newsUrl' => $newsUrl,
        ];
    }

    public function getNews($id): \Illuminate\Http\JsonResponse
    {

        $this->increaseCount($id);
        $news = $this->newsById($id);
        return response()->json($news);
    }

    public function getAllNewsByCategory($categoryId, $limit, $skip)
    {
        $newsIds = News::where('published', 1)->join('news_categories', 'news_categories.news_id', 'news.id')
            ->where('news_categories.category_id', $categoryId)
            ->select('news.id')
            ->orderBy('news.date', 'DESC')
            ->orderBy('news.order', 'asc')
            ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
            ->skip($skip)
            ->take($limit)
            ->get();
        $news = [];
        foreach ($newsIds as $id) {
            $news[] = $this->newsById($id->id);
        }

        $category = Category::find($categoryId);
        if ($category) {
            $category->url = $category->route ?? '/' . ($category->slug ?? 'category-' . $category->id);
        }
        return response()->json([
            'category' => $category,
            'news' => $news
        ]);
    }

    public function getAllNewsBySubCategory($subCategoryId, $limit, $skip)
    {
        $newsIds = News::where('published', 1)
            ->join('news_sub_categories', 'news_sub_categories.news_id', 'news.id')
            ->where('news_sub_categories.sub_category_id', $subCategoryId)
            ->orderBy('news.date', 'DESC')
            ->orderBy('news.order', 'asc')
            ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
            ->select('news.id')
            ->skip($skip)
            ->take($limit)
            ->get();

        $news = [];
        foreach ($newsIds as $id) {
            $news[] = $this->newsById($id->id);
        }

        $subCategory = Subcategory::find($subCategoryId);
        if ($subCategory) {
            $subCategory->url = $subCategory->route ?? '/' . ($subCategory->slug ?? 'subcategory-' . $subCategory->id);
            $parentCategory = Category::find($subCategory->category_id);
            if ($parentCategory) {
                $categorySlug = $parentCategory->slug ?? 'category-' . $parentCategory->id;
                $subcategorySlug = $subCategory->slug ?? 'subcategory-' . $subCategory->id;
                $subCategory->url = "/{$categorySlug}/{$subcategorySlug}";
            }
        }
        return response()->json([
            'sub-category' => $subCategory,
            'news' => $news
        ]);
    }

    /**
     * Get all news by category slug (Prothom Alo style)
     * Format: /{categorySlug}/{limit}/{skip}
     */
    public function getAllNewsByCategorySlug($categorySlug, $limit, $skip)
    {
        // Find category by slug
        $category = Category::where('slug', $categorySlug)->where('visible', 1)->first();

        if (!$category) {
            return response()->json([
                'error' => 'Category not found'
            ], 404);
        }

        // Get news by category
        $newsIds = News::where('published', 1)
            ->join('news_categories', 'news_categories.news_id', 'news.id')
            ->where('news_categories.category_id', $category->id)
            ->select('news.id')
            ->orderBy('news.date', 'DESC')
            ->orderBy('news.order', 'asc')
            ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
            ->skip($skip)
            ->take($limit)
            ->get();

        $news = [];
        foreach ($newsIds as $id) {
            $news[] = $this->newsById($id->id);
        }

        $category->url = $category->route ?? '/' . ($category->slug ?? 'category-' . $category->id);

        return response()->json([
            'category' => $category,
            'news' => $news
        ]);
    }

    /**
     * Get news by slug (Prothom Alo style)
     * Format: /{categorySlug}/{subcategorySlug}/{newsKey}
     */
    public function getNewsBySlug($categorySlug, $subcategorySlug, $newsKey)
    {
        // Find category by slug
        $category = Category::where('slug', $categorySlug)->where('visible', 1)->first();

        if (!$category) {
            return response()->json([
                'error' => 'Category not found'
            ], 404);
        }

        // Find subcategory if provided
        $subcategory = null;
        if ($subcategorySlug && $subcategorySlug !== 'null') {
            $subcategory = Subcategory::where('slug', $subcategorySlug)
                ->where('category_id', $category->id)
                ->where('visible', 1)
                ->first();

            if (!$subcategory) {
                return response()->json([
                    'error' => 'Subcategory not found'
                ], 404);
            }
        }

        // Find news by slug (news key)
        $query = News::where('published', 1)
            ->join('news_categories', 'news_categories.news_id', 'news.id')
            ->where('news_categories.category_id', $category->id)
            ->where('news.slug', $newsKey);

        if ($subcategory) {
            $query->join('news_sub_categories', 'news_sub_categories.news_id', 'news.id')
                ->where('news_sub_categories.sub_category_id', $subcategory->id);
        } else {
            $query->leftJoin('news_sub_categories', 'news_sub_categories.news_id', 'news.id')
                ->whereNull('news_sub_categories.sub_category_id');
        }

        $news = $query->select('news.*')->first();

        if (!$news) {
            return response()->json([
                'error' => 'News not found'
            ], 404);
        }

        // Get full news data
        $newsData = $this->newsById($news->id);

        // Add category and subcategory info
        $newsData['category_info'] = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'url' => '/' . $category->slug
        ];

        if ($subcategory) {
            $newsData['subcategory_info'] = [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'slug' => $subcategory->slug,
                'url' => '/' . $category->slug . '/' . $subcategory->slug
            ];
        }

        return response()->json($newsData);
    }

    /**
     * Get all news by subcategory slug (Prothom Alo style)
     * Format: /{categorySlug}/{subcategorySlug}
     */
    public function getAllNewsBySubCategorySlug($categorySlug, $subcategorySlug)
    {
        // Find category by slug
        $category = Category::where('slug', $categorySlug)->where('visible', 1)->first();

        if (!$category) {
            return response()->json([
                'error' => 'Category not found'
            ], 404);
        }

        // Find subcategory by slug
        $subcategory = Subcategory::where('slug', $subcategorySlug)
            ->where('category_id', $category->id)
            ->where('visible', 1)
            ->first();

        if (!$subcategory) {
            return response()->json([
                'error' => 'Subcategory not found'
            ], 404);
        }

        // Get news by subcategory
        $newsIds = News::where('published', 1)
            ->join('news_sub_categories', 'news_sub_categories.news_id', 'news.id')
            ->where('news_sub_categories.sub_category_id', $subcategory->id)
            ->select('news.id')
            ->orderBy('news.date', 'DESC')
            ->orderBy('news.order', 'asc')
            ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
            ->take(50)
            ->get();

        $news = [];
        foreach ($newsIds as $id) {
            $news[] = $this->newsById($id->id);
        }

        $category->url = '/' . ($category->slug ?? 'category-' . $category->id);
        $subcategory->url = '/' . $category->slug . '/' . ($subcategory->slug ?? 'subcategory-' . $subcategory->id);

        return response()->json([
            'category' => $category,
            'subcategory' => $subcategory,
            'news' => $news
        ]);
    }

    /**
     * Get news by slug without subcategory (Prothom Alo style)
     * Format: /{categorySlug}/{newsKey}
     */
    public function getNewsBySlugWithoutSubcategory($categorySlug, $newsKey)
    {
        // Find category by slug
        $category = Category::where('slug', $categorySlug)->where('visible', 1)->first();

        if (!$category) {
            return response()->json([
                'error' => 'Category not found'
            ], 404);
        }

        // Find news by slug (news key) - without subcategory
        $news = News::where('published', 1)
            ->join('news_categories', 'news_categories.news_id', 'news.id')
            ->where('news_categories.category_id', $category->id)
            ->where('news.slug', $newsKey)
            ->leftJoin('news_sub_categories', 'news_sub_categories.news_id', 'news.id')
            ->whereNull('news_sub_categories.sub_category_id')
            ->select('news.*')
            ->first();

        if (!$news) {
            return response()->json([
                'error' => 'News not found'
            ], 404);
        }

        // Get full news data
        $newsData = $this->newsById($news->id);

        // Add category info
        $newsData['category_info'] = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'url' => '/' . $category->slug
        ];

        return response()->json($newsData);
    }

    /**
     * Get all news by subcategory slug with pagination (Prothom Alo style)
     * Format: /{categorySlug}/{subcategorySlug}/{limit}/{skip}
     */
    public function getAllNewsBySubCategorySlugPaginated($categorySlug, $subcategorySlug, $limit, $skip)
    {
        // Find category by slug
        $category = Category::where('slug', $categorySlug)->where('visible', 1)->first();

        if (!$category) {
            return response()->json([
                'error' => 'Category not found'
            ], 404);
        }

        // Find subcategory by slug
        $subcategory = Subcategory::where('slug', $subcategorySlug)
            ->where('category_id', $category->id)
            ->where('visible', 1)
            ->first();

        if (!$subcategory) {
            return response()->json([
                'error' => 'Subcategory not found'
            ], 404);
        }

        // Get news by subcategory with pagination
        $newsIds = News::where('published', 1)
            ->join('news_sub_categories', 'news_sub_categories.news_id', 'news.id')
            ->where('news_sub_categories.sub_category_id', $subcategory->id)
            ->select('news.id')
            ->orderBy('news.date', 'DESC')
            ->orderBy('news.order', 'asc')
            ->where('news.date', '<', date('Y-m-d H:i:s', strtotime(Date('Y-m-d') . ' +1 day')))
            ->skip($skip)
            ->take($limit)
            ->get();

        $news = [];
        foreach ($newsIds as $id) {
            $news[] = $this->newsById($id->id);
        }

        $category->url = '/' . ($category->slug ?? 'category-' . $category->id);
        $subcategory->url = '/' . $category->slug . '/' . ($subcategory->slug ?? 'subcategory-' . $subcategory->id);

        return response()->json([
            'category' => $category,
            'sub-category' => $subcategory, // Keep both for backward compatibility
            'subcategory' => $subcategory,
            'news' => $news
        ]);
    }
}
