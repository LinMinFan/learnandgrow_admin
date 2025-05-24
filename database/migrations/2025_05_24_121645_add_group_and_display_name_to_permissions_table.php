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
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedInteger('permission_group_id')->after('id')->nullable()->comment('關聯權限群組');
            $table->string('display_name')->after('name')->nullable()->comment('顯示名稱（中文）');

            $table->foreign('permission_group_id')->references('id')->on('permission_groups')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['permission_group_id']);
            $table->dropColumn(['permission_group_id', 'display_name']);
        });
    }
};
