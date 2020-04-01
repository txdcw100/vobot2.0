<?php

namespace Robot\Models;


use App\Models\Model;
use App\Models\Tenant\TntGroup;
use Illuminate\Database\Eloquent\Builder;

class RobotCate extends Model
{
    const Cate_STATUS_STOP = 0; //停止
    const Cate_STATUS_NORMAL = 1;//正常

    protected $guarded = [];

    protected $casts = ['category_id' => 'array'];


    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNormal(Builder $query){
        return $query->where('status', self::Cate_STATUS_NORMAL);
    }

    /**
     * 门店
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tntgroups(){
        return $this->belongsTo(TntGroup::class, 'store_id');
    }
}
