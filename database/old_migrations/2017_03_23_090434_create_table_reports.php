<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('object_id');
            $table->string('object_type');
            $table->string('reason')->comment('举报原因');
            $table->integer('status')->comment('0,待处理。1,已删除。2,已忽略');
            $table->foreign('last_op_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('last_op_time')->comment('处理时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reports');
    }
}
