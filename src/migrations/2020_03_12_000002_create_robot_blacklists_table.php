<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotBlackListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_blacklists')){
            Schema::create('robot_blacklists', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->tinyInteger('friend_id')->default(0)->comment('群用户ID');
                $table->tinyInteger('user_id')->default(0)->comment('屏蔽操作人ID');
                $table->tinyInteger('tenant_id')->default(0)->comment('商户ID');
                $table->text('reason')->nullable(0)->comment('屏蔽原因');
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
        Schema::dropIfExists('robot_blacklists');
    }
}
