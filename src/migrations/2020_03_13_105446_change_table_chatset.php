<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableChatset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE robot_group_friends CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("ALTER TABLE robot_groups CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("ALTER TABLE robot_assistants CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("ALTER TABLE robot_assistant_chatrooms CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
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
