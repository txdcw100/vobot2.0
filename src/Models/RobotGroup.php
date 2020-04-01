<?php

namespace Robot\Models;


use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

class RobotGroup extends Model
{
    const GROUP_STATUS_STOP = 0; //停止
    const GROUP_STATUS_NORMAL = 1;//正常

    protected $guarded = [];

    /**
     * 机器人政策
     * @return array
     */
    public static function getAllTypes()
    {
        return [ 1=>'门店微信群', 2=>'会员微信群'];
    }

    public static function getAllRoles()
    {
        return ['管理员','群成员'];
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * 获取群助手
     */
    public function belongsToAssistant()
    {
        return $this->belongsTo(RobotAssistant::class,'assistant_id');
    }

    /**
     * 群成员
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function friends()
    {
        return $this->hasMany(RobotGroupFriend::class, 'group_id');
    }

    /**
     * 群消息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(RobotGroupMessage::class, 'group_id');
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNormal(Builder $builder)
    {
        return $builder->where('status', self::GROUP_STATUS_NORMAL);
    }

    /**
     * 分类
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cates()
    {
        return $this->belongsTo(RobotCate::class, 'cate_id');
    }

}
