<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-10
 * Time: 09:47
 */

namespace Robot\Models;


use App\Models\Model;

class RobotGroupFriend extends Model
{
    protected $guarded = [];

    /**
     * 群
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groups()
    {
        return $this->belongsTo(RobotGroup::class, 'group_id');
    }

    /**
     * 成员消息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(RobotGroupMessage::class, 'friend_id');
    }

    /**
     * 成员
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function friends()
    {
        return $this->hasMany(self::class, 'wx_id', 'wx_id');
    }
}
