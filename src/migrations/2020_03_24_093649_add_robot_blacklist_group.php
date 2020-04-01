<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRobotBlacklistGroup extends Migration
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
            if(!Schema::hasColumn('robot_blacklists','group_id')){
                $table->string('group_id',255)->after('tenant_id')->comment('成员所在微信群ID');
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
