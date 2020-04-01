<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRobotGroupMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('robot_group_messages')){
            Schema::create('robot_group_messages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('friend_id')->default(0)->comment('群成员ID');
                $table->integer('group_id')->index()->default(0)->comment('群ID');
                $table->char('wx_id',50)->index()->nullable(0)->comment('微信号');
                $table->char('msg_type', 10)->index()->nullable(0)->comment('消息类型');
                $table->char('msg_id', 30)->nullable(0)->comment('robot 消息ID');
                $table->text('content')->nullable(0)->charset('utf8mb4')->collation('utf8mb4_general_ci')->comment('内容');
                $table->timestamps();
            });
            DB::statement("ALTER TABLE robot_group_messages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        }
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
