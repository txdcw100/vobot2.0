<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotAssistantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_assistants')){
            Schema::create('robot_assistants', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('wx_uid', 50)->comment('微信用户身份标识');
                $table->string('wx_id', 50)->comment('微信ID');
                $table->integer('user_id')->default(0)->comment('管理员ID');
                $table->tinyInteger('status')->default(0)->comment('0、停止 1、正常');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('robot_assistants');
    }
}
