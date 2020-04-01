<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-10
 * Time: 09:47
 */

namespace Robot\Models;


use App\Models\Model;
use App\Models\User;

class RobotBlacklist extends Model
{
    protected $guarded = [];
    protected $casts = ['group_id' => 'array'];

    /**
     * 操作者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 群助手
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assistants()
    {
        return $this->belongsTo(RobotAssistant::class, 'assistant_id');
    }

    public function friend(){
        return $this->belongsTo(RobotGroupFriend::class, 'wx_id','wx_id');
    }

}
