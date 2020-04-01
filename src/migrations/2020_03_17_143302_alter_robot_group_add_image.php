<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRobotGroupAddImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('robot_groups', function (Blueprint $table){
            if(!Schema::hasColumn('robot_groups','qucode_img')){
                $table->string('qrcode_img')->after('qrcode')->nullable()->comment('群二维码图片');
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
