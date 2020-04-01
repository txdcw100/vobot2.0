<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-10
 * Time: 09:47
 */

namespace Robot\Models;


use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

class RobotAssistant extends Model
{
    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    protected $guarded = [];

    /**

     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyGroups()
    {
        return $this->hasMany(RobotGroup::class, 'assistant_id');

    }
    /**
     * 正常
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNormal(Builder $builder)
    {
        return $builder->where('status', self::STATUS_ON);

    }

}
