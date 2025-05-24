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
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('選單名稱');
            $table->string('icon')->nullable()->comment('選單圖示');
            $table->string('route')->nullable()->comment('路由');
            $table->unsignedInteger('parent_id')->nullable()->comment('父選單ID');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('是否顯示');
            $table->unsignedBigInteger('permission_id')->nullable()->comment('綁定權限');

            $table->foreign('permission_id')->references('id')->on('permissions')->nullOnDelete();
            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `menus` comment '後台選單'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
