<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRobotAssistant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robot_assistants', function(Blueprint $table){
            if(!Schema::hasColumn('robot_assistants','nickname')){
                $table->string('nickname', 50)->nullbale()->commment('微信昵称');
            }
            if(!Schema::hasColumn('robot_assistants','alias')){
                $table->string('alias', 50)->nullbale()->commment('微信号');
            }
            if(!Schema::hasColumn('robot_assistants','avatar')){
                $table->string('avatar')->nullable()->comment('头像');
            }
            if(!Schema::hasColumn('robot_assistants','source')){
                $table->char('source', 10)->default('api')->comment('登录来源');
            }
        });
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
