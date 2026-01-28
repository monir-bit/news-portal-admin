<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/common', [\App\Http\Controllers\Api\CommonController::class, 'common']);
Route::get('/home', [\App\Http\Controllers\Api\HomeController::class, 'homeInitial']);
Route::get('/news-details/{slug}', [\App\Http\Controllers\Api\NewsController::class, 'newsDetails']);
Route::get('/news-by-category/{slug}', [\App\Http\Controllers\Api\NewsController::class, 'newsByCategory']);
