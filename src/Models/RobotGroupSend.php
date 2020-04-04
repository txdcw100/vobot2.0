<?php

namespace Robot\Models;


use App\Models\Model;


class RobotGroupSend extends Model
{
    //
    protected $guarded = [];

    public $timestamps = false;

    public function groups()
    {
        return $this->belongsTo(RobotGroup::class, 'group_id');
    }
}
