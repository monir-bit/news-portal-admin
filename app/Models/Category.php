<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'position',
        'visible',
        'parent_id',
    ];

    /**
     * Parent category (belongs to)
     */
    public function parent()
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    /**
     * Direct children categories
     */
    public function children()
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        )->orderBy('position');
    }

    /**
     * Recursive children (tree)
     */
    public function childrenRecursive()
    {
        return $this->children()
            ->with('childrenRecursive');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }
}
