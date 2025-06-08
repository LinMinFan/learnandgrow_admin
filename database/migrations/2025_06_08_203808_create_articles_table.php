<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->comment('文章類別');
            $table->string('title')->comment('標題');
            $table->string('slug')->unique()->comment('文章代號');
            $table->text('excerpt')->nullable()->comment('摘要');
            $table->longText('content')->comment('文章內容');
            $table->string('cover_image')->nullable()->comment('封面圖片');
            $table->enum('status', ['draft', 'published'])->default('draft')->comment('狀態');
            $table->timestamp('published_at')->nullable()->comment('發布時間');
            $table->unsignedBigInteger('author_id')->comment('文章作者');
            $table->boolean('is_top')->default(false)->comment('是否置頂');

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `articles` comment '文章'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
