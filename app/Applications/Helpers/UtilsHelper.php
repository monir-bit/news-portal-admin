<?php

namespace App\Applications\Helpers;

use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UtilsHelper
{
    public static function generateUniqueNewsSlugKey(){
        do {
            $key = Str::lower(Str::random(10));
        } while (News::where('slug_key', $key)->exists());

        return $key;
    }


    static public function GetMediaUrl(?string $path, ?string $disk = 'public'): ?string
    {
        if (! $path) return null;

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }


        $disk = $disk ?? config('filesystems.default');

        return Storage::disk($disk)->url($path);
    }


    static function MonthYearWisePath(): string
    {
        return 'uploads/'.date('Y') . '/' . date('m');
    }

}
