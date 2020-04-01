<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRobotBlackListColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('robot_blacklists', function(Blueprint $table){
           if(Schema::hasColumn('robot_blacklists','friend_id')){
               $table->dropColumn('friend_id');
           }
           if(Schema::hasColumn('robot_blacklists','tenant_id')){
               $table->integer('tenant_id')->change();
           }
            if(Schema::hasColumn('robot_blacklists','user_id')){
                $table->integer('user_id')->change();
            }
        });
        Schema::table('robot_groups', function(Blueprint $table){
            if(Schema::hasColumn('robot_groups','tenant_id')){
                $table->integer('tenant_id')->change();
            }
            if(Schema::hasColumn('robot_groups','user_id')){
                $table->integer('user_id')->change();
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
