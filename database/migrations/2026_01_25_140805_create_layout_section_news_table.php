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
        Schema::create('layout_section_news', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('layout_section_id')->unsigned();
            $table->bigInteger('news_id')->unsigned();
            $table->integer('position')->default(1);
            $table->timestamps();
            $table->unique(['layout_section_id', 'position']);
            $table->foreign('layout_section_id')->references('id')->on('layout_sections')->onDelete('cascade');
            $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layout_section_news');
    }
};
