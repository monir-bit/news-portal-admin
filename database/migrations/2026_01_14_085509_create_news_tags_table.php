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
        Schema::create('news_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->bigInteger('news_id')->index();
            $table->timestamps();
            $table->foreign('news_id')->references('id')->on('news')->cascadeOnDelete();
            $table->unique(['name', 'news_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_tags');
    }
};
