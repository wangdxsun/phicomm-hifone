<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQaModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //taggables表追加时间戳
        Schema::table('taggables', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });



        //tags表 type改为tag_type_id,count明确意义增加备注
        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('type', 'tag_type_id')->comment('标签类型id')->change();
            $table->string('count')->comment('该标签下的对象数')->change();
        });
        //follows表追加answer_count 新回答
        Schema::table('follows', function ($table) {
            $table->unsignedInteger('answer_count')->comment('关注问题的新回答数')->after('followable_type');
        });
        //users表追加is_expert 追加question_count 追加answer_count 追加comment_count 追加notification_qa_count
        Schema::table('users', function ($table) {
            $table->unsignedInteger('question_count')->comment('提问数')->after('score');
            $table->unsignedInteger('answer_count')->comment('回答数')->after('question_count');
            $table->unsignedInteger('comment_count')->comment('回答的评论数')->after('answer_count');
            $table->unsignedInteger('notification_qa_count')->comment('问答的通知数')->after('notification_follow_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taggables', function (Blueprint $table) {
            $table->dropColumn(['id', 'created_at', 'updated_at']);
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('tag_type_id', 'type')->comment('')->change();
            $table->string('count')->comment('')->change();
        });
        Schema::table('follows', function (Blueprint $table) {
            $table->dropColumn('answer_count');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['question_count', 'answer_count', 'comment_count','notification_qa_count']);
        });
    }
}
