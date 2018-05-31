<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFollowsTable extends Migration
{
    /**o
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //follows表追加answer_count 新回答
        Schema::table('follows', function ($table) {
            $table->unsignedInteger('answer_count')->comment('关注问题的新回答数')->after('followable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('follows', function (Blueprint $table) {
            $table->dropColumn('answer_count');
        });
    }
}
