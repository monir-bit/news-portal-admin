<?php

namespace App\Models;

use App\Applications\Helpers\UtilsHelper;
use App\Observers\NewsObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(NewsObserver::class)]
class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shoulder',
        'slug_key',
        'title',
        'ticker',
        'sort_description',
        'order',
        'proofreader',
        'image',
        'type',
        'published',
        'latest',
        'news_marquee',
        'live_news',
        'is_visible_shoulder',
        'is_visible_ticker',
        'category_id',
        'date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published' => 'boolean',
        'latest' => 'boolean',
        'news_marquee' => 'boolean',
        'live_news' => 'boolean',
        'is_visible_shoulder' => 'boolean',
        'is_visible_ticker' => 'boolean',
        'date' => 'datetime',
    ];

    /**
     * Get the category that owns the news
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who created the news
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the news
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function details() {
        return $this->hasOne(NewsDetails::class);
    }

    public function tags() {
        return $this->hasMany(NewsTag::class);
    }

    public function latestNews(){
        return $this->hasOne(LatestNews::class);
    }

    public function marqueeNews(){
        return $this->hasOne(MarqueNews::class);
    }

    public function timelineNews(){
        return $this->hasMany(NewsTimeline::class);
    }

    public function correspondence(){
        return $this->hasOne(NewsCorrespondent::class);
    }

    public function getImageAttribute($value)
    {
        return UtilsHelper::GetMediaUrl($value);
    }

    public function sectionLayoutNews(): HasOne{
        return $this->hasOne(LayoutSectionNews::class, 'news_id', 'id');
    }

}
