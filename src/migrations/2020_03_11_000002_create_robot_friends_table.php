<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_friends')) {
            Schema::create('robot_friends', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 50)->comment('微信昵称');
                $table->string('wx_id', 50)->comment('微信号');
                $table->string('avatar', 255)->comment('微信头像');
                $table->tinyInteger('assistant_id')->default(0)->comment('助手ID');
                $table->tinyInteger('touch')->default(0)->comment('是否是好友 0 、不是 1、是');
                $table->tinyInteger('sex')->default(0)->comment('性别 1 男 2 女 0未知');
                $table->string('province', 20)->nullable()->comment('省份');
                $table->string('city', 20)->nullable()->comment('城市');
                $table->string('country', 20)->nullable()->comment('国家');
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
        Schema::dropIfExists('robot_friends');
    }
}
