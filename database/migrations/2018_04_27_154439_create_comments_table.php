<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('body')->comment('内容');
            $table->text('body_original')->comment('原始内容');
            $table->string('bad_word')->comment('敏感词')->nullable();
            $table->tinyInteger('status')->comment('评论状态 0正常，-1审核未通过，-2待审核，-3已删除')->default(-2);
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('answer_id')->comment('该评论所属回答的id');
            $table->unsignedInteger('comment_id')->comment('该回复所属评论的id')->nullable();
            $table->tinyInteger('order')->comment('评论置顶下沉：0正常，1置顶')->default(0);
            $table->tinyInteger('device')->comment('评论设备 0:H5；1：Android；2：iOS；3：Web')->nullable();
            $table->unsignedInteger('like_count')->comment('点赞数')->default(0);
            $table->string('ip')->comment('用户ip')->nullable();
            $table->unsignedInteger('last_op_user_id')->comment('后台最后操作人id')->nullable();
            $this->timestamp('last_op_time')->comment('后台最后操作时间')->nullable();
            $table->string('last_op_reason')->comment('操作原因')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('answer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments');
    }
}
