<?php

namespace Robot\Models;


use App\Models\Model;


class RobotKeyword extends Model
{
    const KEYWORD_STATUS_STOP = 0; //停止
    const KEYWORD__STATUS_NORMAL = 1;//正常

    protected $guarded = [];

    /**
     * 关键词表
     * @return [type]
     */
    public function items()
    {
        return $this->hasMany(RobotKeywordItem::class, 'keyword_id');
    }

    /**
     * 保存中间表
     * @return [type]
     */
    public function hasgroups()
    {
        return $this->hasMany(RobotKeywordGroup::class, 'keyword_id');
    }

    public function belongsToMaterial()
    {
        return $this->belongsTo(RobotMaterial::class,'material_id');
    }

    /**
     * @param  Builder
     * @return [type]
     */
    public function scopeNormal(Builder $builder)
    {
        return $builder->where('status', self::STATUS_NORMAL);
    }

}
