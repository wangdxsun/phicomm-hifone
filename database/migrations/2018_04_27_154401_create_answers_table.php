<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('body')->comment('内容');
            $table->text('body_original')->comment('原始内容');
            $table->string('bad_word')->comment('敏感词')->nullable();
            $table->string('thumbnails')->comment('缩略图')->nullable();
            $table->text('excerpt')->comment('摘要')->nullable();
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('question_id')->comment('问题id');
            $table->tinyInteger('adopted')->comment('是否已采纳：1已采纳， 0未采纳')->default(0);
            $table->tinyInteger('order')->comment('回答置顶下沉：0正常，1置顶，-1下沉')->default(0);
            $table->tinyInteger('device')->comment('回答设备 0:H5；1：Android；2：iOS；3：Web')->nullable();
            $table->tinyInteger('status')->comment('回答状态 0正常，-1审核未通过，-2待审核，-3已删除')->default(-2);
            $table->unsignedInteger('like_count')->comment('点赞数')->default(0);
            $table->unsignedInteger('comment_count')->comment('评论数')->default(0);
            $table->string('ip')->comment('用户ip')->nullable();
            $table->unsignedInteger('last_op_user_id')->comment('后台最后操作人id')->nullable();
            $table->timestamp('last_op_time')->comment('后台最后操作时间')->nullable();
            $table->string('last_op_reason')->comment('操作原因')->nullable();
            $table->timestamp('edit_time')->comment('最新编辑时间')->nullable();
            $table->timestamp('last_comment_time')->comment('最新评论时间')->nullable();

            $table->timestamps();
            $table->softDeletes();

            //索引
            $table->index('user_id');
            $table->index('question_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
