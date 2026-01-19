<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function getAllImage(): \Illuminate\Http\JsonResponse
    {
        $image = Image::orderBy('id','desc')->groupBy('cdate')->get()->take(8);
        return response()->json($image);
    }

    public function getImage($id): \Illuminate\Http\JsonResponse
    {
        $image = Image::find($id);
        return response()->json($image);
    }
}