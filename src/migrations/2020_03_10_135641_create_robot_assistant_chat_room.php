<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotAssistantChatRoom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_assistant_chatrooms')){
            Schema::create('robot_assistant_chatrooms', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('user_id')->default(0)->comment('管理员ID');
                $table->integer('assistant_id')->default(0)->comment('群助手ID');
                $table->string('wx_id', 50)->comment('微信ID');
                $table->string('nickname', 100)->nullable()->comment('群昵称');
                $table->string('qrcode')->nullable()->comment('群二维码');
                $table->string('head_pic')->nullable()->comment('头像');
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
