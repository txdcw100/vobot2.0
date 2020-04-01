<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRobotGroupColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robot_groups', function(Blueprint $table){
            if(Schema::hasColumn('robot_groups','wx_role')){
                $table->dropColumn('wx_role');
            }
            if(Schema::hasColumn('robot_groups','member_count')){
                $table->dropColumn('member_count');
            }
            if(!Schema::hasColumn('robot_groups','cate_id')){
                $table->integer('cate_id')->default(0)->comment('分类ID');
            }
            if(!Schema::hasColumn('robot_groups','robot_group_id')){
                $table->integer('robot_group_id')->default(0)->comment('微信机器人群ID');
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
