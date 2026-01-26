<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id');
            $table->string('slug_key')->unique();
            $table->text('shoulder')->nullable();
            $table->string('title');
            $table->text('ticker')->nullable();
            $table->longText('sort_description');
            $table->integer('order')->nullable();
            $table->integer('proofreader')->nullable()->default(0);
            $table->string('image')->nullable();
            $table->string('type');
            $table->boolean('published')->default(0);
            $table->boolean('latest')->default(0);
            $table->boolean('news_marquee')->default(0);
            $table->boolean('live_news')->default(0);
            $table->boolean('is_visible_shoulder')->default(true);
            $table->boolean('is_visible_ticker')->default(true);
            $table->dateTime('date');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
