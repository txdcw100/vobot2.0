<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRobotGroupFriend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_group_friends')){
            Schema::create('robot_group_friends', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('group_id')->default(0)->comment('群ID');
                $table->char('nickname',50)->nullable()->comment('用户昵称');
                $table->char('wx_id',50)->nullable()->comment('微信号');
                $table->string('avatar')->nullable()->comment('微信头像');
                $table->integer('group_count')->default(0)->comment('加入群数量');
                $table->integer('message_count')->default(0)->comment('发送消息数量');
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
        //
    }
}
