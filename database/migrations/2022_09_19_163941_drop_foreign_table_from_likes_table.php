<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('likes', function (Blueprint $table) {
        //     $table->dropForeign('likes_story_id_foreign');
        //     $table->dropIndex('likes_story_id_foreign');
        // });
        // Schema::table('comments', function (Blueprint $table) {
        //     $table->dropForeign('comments_story_id_foreign');
        //     $table->dropIndex('comments_story_id_foreign');
        // });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('likes', function (Blueprint $table) {
        //     $table->foreign('story_id')->on('stories')->references('id');
        // });
        // Schema::table('comments', function (Blueprint $table) {
        //     $table->foreign('story_id')->on('stories')->references('id');
        // });
    }
};
