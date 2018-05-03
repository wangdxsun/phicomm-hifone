<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //tags表，明确channel字段的备注
        Schema::table('tags', function (Blueprint $table) {
            $table->unsignedInteger('channel')->comment('channel标志是否为自动标签：0代表自动标签，其他代表不是自动标签')->nullable()->change();
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
        Schema::table('tags', function (Blueprint $table) {
            $table->unsignedInteger('channel')->comment('')->nullable()->change();
        });

        Schema::table('tag_types', function (Blueprint $table) {
            $table->integer('type')->comment('')->change();
        });
    }
}
