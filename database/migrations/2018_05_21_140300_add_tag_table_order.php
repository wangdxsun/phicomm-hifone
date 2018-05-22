<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTagTableOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //tags表,增加order字段
        Schema::table('tags', function (Blueprint $table) {
            $table->unsignedInteger('order')->comment('标签的排序')->nullable()->after('channel');
        });

        //tag_types表,增加order字段
        Schema::table('tag_types', function (Blueprint $table) {
            $table->unsignedInteger('order')->comment('标签分类的排序')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('tag_types', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
