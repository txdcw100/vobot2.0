<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_groups')){
            Schema::create('robot_groups', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',50)->comment('群名称');
                $table->text('qrcode')->comment('群二维码');
                $table->tinyInteger('user_id')->default(0)->comment('管理员ID');
                $table->integer('assistant_id')->default(0)->comment('群助手ID');
                $table->tinyInteger('wx_role')->default(0)->comment('群内助手类型 0、管理员 1、群成员');
                $table->tinyInteger('type')->default(0)->comment('群添加方式 0、API 1、小酷微群助手');
                $table->integer('member_count')->default(0)->comment('群人数');
                $table->char('wx_id', 30)->nullable()->comment('群wxid');
                $table->string('chat_room_owner', 50)->nullable()->comment('群管理员wxid');
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
        Schema::dropIfExists('robot_groups');
    }
}
