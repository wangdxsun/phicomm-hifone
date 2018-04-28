<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('from_user_id')->comment('发起者');
            $table->unsignedInteger('to_user_id')->comment('被邀请者');
            $table->unsignedInteger('question_id')->comment('问题id');
            $table->timestamps();

            //唯一索引
            $table->unique('question_id', 'to_user_id', 'from_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invites');
    }
}
