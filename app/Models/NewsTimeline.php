<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsTimeline extends Model
{
    protected $fillable = ['news_id', 'title', 'details', 'date'];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
