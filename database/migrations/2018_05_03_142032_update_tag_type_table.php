<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTagTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //tag_types表,增加order字段
        Schema::table('tag_types', function (Blueprint $table) {
            $table->unsignedInteger('order')->comment('标签分类的排序')->nullable();
        });

        Schema::table('tag_types', function (Blueprint $table) {
            $table->unsignedInteger('type')->comment('type表征标签类型，0：帖子标签，1：用户标签，2：问题标签')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('tag_types', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('tag_types', function (Blueprint $table) {
            $table->integer('type')->comment('')->change();
        });
    }
}
