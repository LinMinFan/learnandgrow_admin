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
        Schema::create('contact_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject')->comment('主題');
            $table->string('name')->comment('聯絡人名稱');
            $table->string('email')->comment('Email');
            $table->string('phone')->nullable()->comment('電話');
            $table->text('message')->comment('詳細內容');
            $table->boolean('is_read')->default(false)->comment('是否已讀');
            $table->timestamp('read_at')->nullable()->comment('已讀時間');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `contact_forms` comment '聯絡表單'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_forms');
    }
};
