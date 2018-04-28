<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('标题');
            $table->longText('body')->comment('内容');
            $table->text('body_original')->comment('原始内容');
            $table->string('bad_word')->comment('敏感词')->nullable();
            $table->string('thumbnails')->comment('缩略图')->nullable();
            $table->text('excerpt')->comment('摘要')->nullable();
            $table->tinyInteger('status')->comment('问题状态 0正常，-1审核未通过，-2待审核，-3已删除')->default(-2);
            $table->integer('score')->comment('悬赏值')->default(0);
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('answer_id')->comment('采纳回答id，标明已纳与否')->nullable();
            $table->tinyInteger('order')->comment('问题置顶下沉：0正常，1置顶，-1下沉')->default(0);
            $table->tinyInteger('device')->comment('提问设备 0:H5；1：Android；2：iOS；3：Web')->nullable();
            $table->tinyInteger('is_excellent')->comment('是否加精')->default(0);
            $table->unsignedInteger('answer_count')->comment('回答数')->default(0);
            $table->unsignedInteger('view_count')->comment('查看数')->default(0);
            $table->unsignedInteger('like_count')->comment('点赞数')->default(0);
            $table->unsignedInteger('follower_count')->comment('关注用户数')->default(0);
            $table->string('ip')->comment('用户ip')->nullable();
            $table->unsignedInteger('last_op_user_id')->comment('后台最后操作人id')->nullable();
            $this->timestamp('last_op_time')->comment('后台最后操作时间')->nullable();
            $table->string('last_op_reason')->comment('操作原因')->nullable();
            $this->timestamp('edit_time')->comment('最新编辑时间')->nullable();
            $this->timestamp('last_answer_time')->comment('最新回答时间')->nullable();

            $table->timestamps();
            $table->softDeletes();

            //索引
            $table->index('user_id');
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
