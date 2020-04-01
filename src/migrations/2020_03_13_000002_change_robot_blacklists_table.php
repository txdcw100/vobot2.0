<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRobotBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robot_blacklists', function (Blueprint $table){
            if(!Schema::hasColumn('robot_blacklists','avatar')){
                $table->string('avatar',255)->after('id')->comment('微信头像');
            }
            if(!Schema::hasColumn('robot_blacklists','wx_id')){
                $table->string('wx_id', 50)->after('id')->comment('微信号');
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
        Schema::dropIfExists('robot_blacklists');
    }
}
