<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //users表 追加question_count 追加answer_count 追加comment_count 追加notification_qa_count
        Schema::table('users', function ($table) {
            $table->unsignedInteger('question_count')->comment('提问数')->after('score');
            $table->unsignedInteger('answer_count')->comment('回答数')->after('question_count');
            $table->unsignedInteger('comment_count')->comment('回答的评论数')->after('answer_count');
            $table->unsignedInteger('notification_qa_count')->comment('问答的通知数')->after('notification_follow_count');
            $table->unsignedInteger('follow_new_answer_count')->comment('关注问题新回答数')->after('notification_qa_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['question_count', 'answer_count', 'comment_count','notification_qa_count', 'follow_new_answer_count']);
        });
    }
}
