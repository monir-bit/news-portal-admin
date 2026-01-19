<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    /**
     * Generate unique slug from name
     */
    public static function generateUniqueSlug($name, $currentId = null)
    {
        // Use Laravel's Str::slug for transliteration
        $slug = Str::slug($name, '-', 'bn');
        
        // Fallback: if empty, create a simple slug
        if (empty($slug)) {
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
            $slug = trim($slug, '-');
        }
        
        // If still empty, use hash
        if (empty($slug)) {
            $slug = substr(md5($name), 0, 8);
        }

        // Make slug unique
        $originalSlug = $slug;
        $counter = 1;

        while (DB::table('subcategories')
            ->where('slug', $slug)
            ->when($currentId, function ($query) use ($currentId) {
                return $query->where('id', '!=', $currentId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
