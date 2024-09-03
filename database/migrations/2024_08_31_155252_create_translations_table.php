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
        Schema::create('translation', function (Blueprint $table) {
            $table->id();
            $table->string('article_id', 255)->comment('文章Id');
            $table->string('langue', 50)->comment('語系');
            $table->string('title', 50)->comment('標題');
            $table->string('content', 255)->comment('內容');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('新增時間');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment('更新時間');
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation');
    }
};
