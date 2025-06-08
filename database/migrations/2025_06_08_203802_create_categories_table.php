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
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('分類名稱');
            $table->string('slug')->unique()->comment('分類代號');
            $table->text('description')->nullable()->comment('分類說明');
            $table->unsignedInteger('parent_id')->nullable()->comment('父分類）');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->integer('sort_order')->default(0)->comment('排序用');

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `categories` comment '文章分類'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
