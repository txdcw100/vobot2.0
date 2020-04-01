<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRobotGroupMessages extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement("ALTER TABLE `robot_group_messages` MODIFY COLUMN `wx_id` char(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '微信ID' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
