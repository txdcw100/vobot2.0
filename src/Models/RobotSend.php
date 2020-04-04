<?php

namespace Robot\Models;


use App\Models\Model;


class RobotSend extends Model
{
    //
    const STATUS_NORMAL = 1;
    const STATUS_STOP = 0; //停止

    const CHANNEL_API = 0;
    const CHANNEL_ASSISTANT=1;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsToMaterial()
    {
        return $this->belongsTo(RobotMaterial::class,'material_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsToAssistant()
    {
        return $this->belongsTo(RobotAssistant::class,'assistant_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToManyGroup()
    {
        return $this->belongsToMany(RobotGroup::class, RobotGroupSend::class, 'send_id', 'group_id')->withTimestamps();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasgroups()
    {
        return $this->hasMany(RobotGroupSend::class, 'send_id');
    }
}
