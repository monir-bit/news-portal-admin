<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LatestNewsController;
use App\Http\Controllers\LiveNewsController;
use App\Http\Controllers\MarqueNewsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsTimelineController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('category')->name('category.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CategoryController::class, 'delete'])->name('delete');
    });

    Route::prefix('tag')->name('tag.')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::post('/store', [TagController::class, 'store'])->name('store');
        Route::post('/update/{id}', [TagController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [TagController::class, 'delete'])->name('delete');
    });

    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/store', [NewsController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [NewsController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [NewsController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [NewsController::class, 'delete'])->name('delete');
    });

    Route::prefix('latest-news')->name('latest-news.')->group(function () {
        Route::get('/', [LatestNewsController::class, 'index'])->name('index');
        Route::delete('/delete/{id}', [LatestNewsController::class, 'delete'])->name('delete');
    });

    Route::prefix('marque-news')->name('marque-news.')->group(function () {
        Route::get('/', [MarqueNewsController::class, 'index'])->name('index');
        Route::delete('/delete/{id}', [MarqueNewsController::class, 'delete'])->name('delete');
    });

    Route::prefix('live-news')->name('live-news.')->group(function () {
        Route::get('/', [LiveNewsController::class, 'index'])->name('index');
        Route::delete('/delete/{id}', [LiveNewsController::class, 'delete'])->name('delete');
    });

    Route::prefix('timeline-news/{news_id}')->name('timeline-news.')->group(function () {
        Route::get('/', [NewsTimelineController::class, 'index'])->name('index');
        Route::post('/store', [NewsTimelineController::class, 'store'])->name('store');
        Route::post('/update/{id}', [NewsTimelineController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [NewsTimelineController::class, 'delete'])->name('delete');
    });
});

require __DIR__.'/settings.php';
