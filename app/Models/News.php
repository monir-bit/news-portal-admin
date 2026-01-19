<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class News extends Model
{
    use HasFactory;
    
    public static function boot()
    {
        parent::boot();
        static::creating(function($model){
            $model->author_id  =   \Auth::user()->id;
        });

        static::updating(function($model){
            $model->author_id  =   \Auth::user()->id;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
    
    public function details(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(NewsDetails::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany('App\Models\NewsCategory', 'news_id');
    }

    public function subCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Subcategory', 'sub_category_id');
    }

    public function region(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Models\Region','news_id');
    }
    
    public function reporter(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Models\WeAre','id', 'reporter_id');
    }

    public function keywordList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\NewsKeyword','news_id');
    }

    public function liveNews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\LiveNews', 'news_id');
    }

    public function format(): array
    {
        // Generate newsUrl (Prothom Alo style)
        $newsUrl = '#';
        $primaryCategory = DB::table('news_categories')
            ->join('categories', 'categories.id', 'news_categories.category_id')
            ->where('news_categories.news_id', $this->id)
            ->select('categories.slug', 'categories.id')
            ->first();
        
        if ($primaryCategory) {
            $categorySlug = $primaryCategory->slug ?? 'category-' . $primaryCategory->id;
            
            // Check for subcategory
            $primarySubCategory = DB::table('news_sub_categories')
                ->join('subcategories', 'subcategories.id', 'news_sub_categories.sub_category_id')
                ->where('news_sub_categories.news_id', $this->id)
                ->select('subcategories.slug', 'subcategories.id')
                ->first();
            
            $newsSlug = $this->slug ?? 'news-' . $this->id;
            
            if ($primarySubCategory) {
                $subcategorySlug = $primarySubCategory->slug ?? 'subcategory-' . $primarySubCategory->id;
                $newsUrl = "/{$categorySlug}/{$subcategorySlug}/{$newsSlug}";
            } else {
                $newsUrl = "/{$categorySlug}/{$newsSlug}";
            }
        }
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'caption' => $this->caption,
            'sort_description' => $this->sort_description,
            'order' => $this->order,
            'category' => $this->category,
            'timeline_id' => $this->timeline_id,
            'image' => $this->image,
            'type' => $this->type,
            'date' => $this->date,
            'newsUrl' => $newsUrl
        ];
    }

    public function formatDetails(): array
    {
        $keyWords = [];
        foreach($this->keywordList as $item ){
            $keyWords[]=[
                'id'=> $item->keywordItem->id,
                'name'=> $item->keywordItem->name,
            ];
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'caption' => $this->caption,
            'sort_description' => $this->sort_description,
            'order' => $this->order,
            'category' => $this->category,
            // 'sub_category' => $this->subCategory->name ?? '',
            'date' => $this->date,
            'image' => $this->image,
            'type' => $this->type,
            'details' => $this->details->details,
            'ticker' => $this->details->ticker,
            'shoulder' => $this->details->shoulder,
            'representative' => $this->details->representative,
            'video_link' => $this->details->video_link,
            'google_drive_link' => $this->details->google_drive_link,
            'audio_link' => $this->details->audio_link,
            'keyword' => $keyWords,
            'timeline_id' => $this->timeline_id,
        ];
    }

    public function filterFormat(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sort_description' => $this->sort_description,
            'order' => $this->order,
            'category' => $this->category,
            'timeline_id' => $this->timeline_id,
            'image' => $this->image,
            'type' => $this->type,
            'date' => $this->date,
            'division' => $this->region->divisionInfo->bn_name ?? '',
            'district' => $this->region->districtInfo->bn_name ?? '',
            'upozilla' => $this->region->upozillaInfo->bn_name ?? '',
        ];
    }

    /**
     * Generate unique 10 character random alphanumeric string (NanoID style)
     * Similar to Prothom Alo's hash ID (e.g., rdq5h5ifq3)
     */
    public static function generateUniqueSlug($title = null, $currentId = null)
    {
        // Characters for random string (alphanumeric lowercase)
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        
        // Generate random 10 character string
        do {
            $slug = '';
            for ($i = 0; $i < 10; $i++) {
                $slug .= $characters[random_int(0, $charactersLength - 1)];
            }
            
            // Check if slug already exists
            $exists = DB::table('news')
                ->where('slug', $slug)
                ->when($currentId, function ($query) use ($currentId) {
                    return $query->where('id', '!=', $currentId);
                })
                ->exists();
        } while ($exists);

        return $slug;
    }
}