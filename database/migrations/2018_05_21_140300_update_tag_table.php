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
        //tags表 type改为tag_type_id,count明确意义增加备注
        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('type', 'tag_type_id')->comment('标签类型id')->change();
            $table->string('count')->comment('该标签下的对象数')->change();
        });
        //tags表,增加order字段
        Schema::table('tags', function (Blueprint $table) {
            $table->unsignedInteger('order')->comment('标签的排序')->nullable()->after('channel');
        });

        //tags表，明确channel字段的备注
        Schema::table('tags', function (Blueprint $table) {
            $table->unsignedInteger('channel')->comment('channel标志是否为自动标签：0代表自动标签，其他代表不是自动标签')->nullable()->change();
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
            $table->renameColumn('tag_type_id', 'type')->comment('')->change();
            $table->string('count')->comment('')->change();
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->unsignedInteger('channel')->comment('')->nullable()->change();
        });

    }
}
