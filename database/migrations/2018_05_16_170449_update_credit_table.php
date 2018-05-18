<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //credits表，新增object_id字段
        Schema::table('credits', function (Blueprint $table) {
            $table->unsignedInteger('object_id')->comment('对象的id')->nullable()->after('body');
            $table->string('object_type')->comment('对象，表明是对帖子、问题等的操作')->nullable()->after('body');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn(['object_type', 'object_id']);
        });
    }
}
