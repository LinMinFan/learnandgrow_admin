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
        Schema::create('media_folders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path')->unique()->comment('資料夾完整路徑');
            $table->string('name')->comment('資料夾名稱');
            $table->unsignedInteger('depth')->default(0)->comment('資料夾層級（0 = 根層）');
            $table->unsignedInteger('parent_id')->nullable()->comment('父選單ID');
            $table->boolean('is_default')->default(false)->comment('是否為預設資料夾');

            $table->foreign('parent_id')->references('id')->on('media_folders')->onDelete('cascade');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `media_folders` comment '媒體庫資料夾'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_folders');
    }
};
