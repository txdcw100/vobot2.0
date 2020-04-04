<?php

namespace Robot\Models;


use App\Models\Model;


class RobotMaterial extends Model
{
    const STATUS_NORMAL = 1;
    const STATUS_CLOSE = 0;
    const TYPE_LINK = 2;
    //
    protected $guarded = [];

    public $appends = ['format_content'];


    /**
     * @return array
     */
    public static function getAllTypes()
    {
        return ['文本','图片', '链接'];
    }

    /**
     * 类型英文名称
     * @return [type]
     */
    public static function getAllTypeEnames()
    {
        return ['text', 'image', 'url'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneKeyword()
    {
        return $this->hasOne(RobotKeyword::class,'material_id');
    }

    /**
     * @return mixed|string
     */
    public function getFormatContentAttribute()
    {
        if($this->type == self::TYPE_LINK){
            return unserialize($this->content);
        }
        return '';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManySend()
    {
        return $this->hasMany(RobotSend::class, 'material_id');

    }

}
