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
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('filename')->comment('檔案名稱');
            $table->string('path')->unique()->comment('儲存路徑');    // storage 的相對路徑
            $table->string('mime_type')->nullable()->comment('檔案類型');
            $table->unsignedBigInteger('size')->nullable()->comment('單位：位元組');
            $table->string('alt')->nullable()->comment('圖片替代文字');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `media` comment '媒體庫'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
