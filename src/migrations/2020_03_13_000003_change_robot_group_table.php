<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRobotGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robot_groups', function (Blueprint $table){
            if(!Schema::hasColumn('robot_groups','tenant_id')){
                $table->tinyInteger('tenant_id')->after('qrcode')->default(0)->comment('商户ID');
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
        Schema::dropIfExists('robot_groups');
    }
}
