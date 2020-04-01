<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotCatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_cates')){
            Schema::create('robot_cates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('tenant_id')->default(0)->comment('供应商ID');
                $table->string('name',50)->comment('分类名称');
                $table->string('category_id',255)->comment('商品品类ID');
                $table->integer('operate_id')->default(0)->comment('操作人ID');
                $table->integer('store_id')->default(0)->comment('门店ID');
                $table->integer('group_count')->default(0)->comment('分类下群数量');
                $table->integer('member_count')->default(0)->comment('分类下人数量');
                $table->string('avatar',255)->comment('分类头像');
                $table->tinyInteger('status')->default(0)->comment('群分类状态0、停止 1、正常');
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
        Schema::dropIfExists('robot_cates');
    }
}
