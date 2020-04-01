<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotAssistantFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_assistant_friends')) {
            Schema::create('robot_assistant_friends', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->char('wx_id', 30)->nullable()->comment('wxid');
                $table->integer('assistant_id')->default(0)->comment('群助手ID');
                $table->string('nickname', 100)->nullable()->comment('群昵称');
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
        Schema::dropIfExists('robot_assistant_friends');
    }
}
