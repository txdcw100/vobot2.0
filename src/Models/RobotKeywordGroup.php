<?php

namespace Robot\Models;


use App\Models\Model;


class RobotKeywordGroup extends Model
{
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function keywords()
    {
        return $this->belongsTo(RobotKeyword::class, 'keyword_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groups()
    {
        return $this->belongsTo(RobotGroup::class, 'group_id');
    }
}
