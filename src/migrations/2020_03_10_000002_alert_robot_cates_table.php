<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertRobotCatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('robot_cates','rule')) {
            Schema::table('robot_cates',function(Blueprint $table) {
            $table->string('rule',50)->after('status')->nullable()->default('')->comment('机器人政策 0：门店微信群 1：会员微信群');
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
        Schema::dropIfExists('robot_cates');
    }
}
