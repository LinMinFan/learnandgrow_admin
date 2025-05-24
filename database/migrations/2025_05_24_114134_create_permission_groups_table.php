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
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('英文名稱/識別用');
            $table->string('display_name')->comment('顯示名稱/中文');
            $table->text('description')->nullable()->comment('說明');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('是否啟用');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_groups');
    }
};
