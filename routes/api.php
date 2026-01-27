<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/common', [\App\Http\Controllers\Api\CommonController::class, 'common']);
Route::get('/home', [\App\Http\Controllers\Api\HomeController::class, 'homeInitial']);
