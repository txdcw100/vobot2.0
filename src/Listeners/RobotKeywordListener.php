<?php

namespace Robot\Listeners;


use Illuminate\Support\Facades\DB;
use Robot\Lib\Keyword;
use Robot\Models\RobotGroup;
use Robot\Models\RobotKeyword;
use Robot\Models\RobotKeywordGroup;
use Robot\Models\RobotKeywordItem;
use Robot\Models\RobotMaterial;

class RobotKeywordListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 保存信息
     * @param $event
     */
    public function onSave($event)
    {
        $datas = ($event->datas);
        if(!$datas) return self::getResult(0, '保存字段数据为空');
        if(!$datas['tenant_id']) return self::getResult(0, '商户信息为空');
        $groupIds = $this->_getGroup($datas['group_id']);
        if(isset($datas['id']) && $datas['id']){
            $keywordId = RobotKeyword::where(['id' => $datas['id'], 'tenant_id' => $datas['tenant_id']])->value('id');
            if($keywordId){
                $result = $this->update($keywordId, $datas);
                app('robot')->getKeyword()->update(
                    $groupIds,
                    $datas
                );
            }
            else{
                return self::getResult(0, '保存失败');
            }
        }else{
            $result = $this->store($datas);
            if($result['status'] == 1) {
                $appResult = app('robot')->getKeyword()->store(
                    $groupIds,
                    $datas
                );
                if($appResult['status'] == 1 && $appResult['data']['id']){
                    RobotKeyword::where('id', $result['data']['id'])->update(['robot_keyword_id' => $appResult['data']['id']]);
                }
            }
        }



    }

    /**
     * 更新
     * @return [type]
     */
    private function update($keywordId, $datas)
    {
        DB::beginTransaction();
        $keyword = RobotKeyword::find($keywordId);
        $keyword->title = $datas['title'];
        $keyword->user_id = $datas['user_id'];
        $keyword->save();

        $material = $keyword->belongsToMaterial;
        $material->content = $this->_getContent($datas['type'], $datas);
        $material->type = $datas['type'];
        $material->user_id = $datas['user_id'];
        $material->save();

        $keyword->items()->delete();
        $keywordItems = [];
        $types = $datas['keyword']['type'];
        $tmpTime = now()->toDateTimeString();
        array_walk($datas['keyword']['title'], function($item, $index) use(&$keywordItems, &$types, &$tmpTime, &$keywordId){
            if(!is_null($item)){
                $keywordItems[] = [
                    'keyword_id' => $keywordId,
                    'name' => $item,
                    'type' => $types[$index],
                    'created_at' => $tmpTime,
                    'updated_at' => $tmpTime,
                ];
            }
        });
        if($keywordItems){
            RobotKeywordItem::insert($keywordItems);
        }
        else{
            DB::rollback();
            return self::getResult(0, '保存关键词参数为空');
        }
        $keyword->hasgroups()->delete();
        $groupItems = [];
        $groupIds = explode(',', $datas['group_id']);
        array_walk($groupIds, function($item) use(&$groupItems, &$tmpTime, &$keywordId){
            $groupItems[] = [
                'keyword_id' => $keywordId,
                'group_id' => $item,
                'created_at' => $tmpTime,
                'updated_at' => $tmpTime,
            ];
        });
        if($groupItems){
            RobotKeywordGroup::insert($groupItems);
        }
        else{
            DB::rollback();
            return self::getResult(0, '保存关键词和群关系参数为空');
        }
        DB::commit();
        return self::getResult(1, 'ok');
    }

    /**
     * 创建
     * @return [type]
     */
    private function store($datas)
    {
        DB::beginTransaction();
        $materialObj = RobotMaterial::create([
            'content' => $this->_getContent($datas['type'], $datas),
            'type' => $datas['type'],
            'user_id' => $datas['user_id'],
            'status' => RobotMaterial::STATUS_NORMAL,
        ]);
        $keywordObj = RobotKeyword::create([
            'title' => $datas['title'],
            'user_id' => $datas['user_id'],
            'tenant_id' => $datas['tenant_id'],
            'status' => RobotKeyword::KEYWORD__STATUS_NORMAL,
            'material_id' => $materialObj->id
        ]);

        $keywordId = $keywordObj->id;
        $keywordItems = [];
        $types = $datas['keyword']['type'];
        $tmpTime = now()->toDateTimeString();

        array_walk($datas['keyword']['title'], function($item, $index) use(&$keywordItems, &$types, &$tmpTime, &$keywordId){
            if(!is_null($item)){
                $keywordItems[] = [
                    'keyword_id' => $keywordId,
                    'name' => $item,
                    'type' => $types[$index],
                    'created_at' => $tmpTime,
                    'updated_at' => $tmpTime,
                ];
            }
        });
        if($keywordItems){
            RobotKeywordItem::insert($keywordItems);
        }
        else{
            DB::rollback();
            return self::getResult(0, '保存关键词参数为空');
        }
        $groupItems = [];
        $groupIds = explode(',', $datas['group_id']);
        array_walk($groupIds, function($item) use(&$groupItems, &$tmpTime, &$keywordId){
            $groupItems[] = [
                'keyword_id' => $keywordId,
                'group_id' => $item,
                'created_at' => $tmpTime,
                'updated_at' => $tmpTime,
            ];
        });
        if($groupItems){
            RobotKeywordGroup::insert($groupItems);
        }
        else{
            DB::rollback();
            return self::getResult(0, '保存关键词和群关系参数为空');
        }
        DB::commit();
        return self::getResult(1, 'ok', ['id' => $keywordId]);
    }


    /**
     * 获取内容
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    protected function _getContent($type, $datas)
    {
        $action = '_get'.ucfirst(RobotMaterial::getAllTypeEnames()[$type]);
        return $this->$action($datas);
    }

    /**
     * 文本
     * @param  [type]
     * @return [type]
     */
    protected function _getText($datas)
    {

        return $datas['txt']['content'];
    }

    /**
     * 图片
     * @param  [type]
     * @return [type]
     */
    protected function _getImage($datas)
    {
        return $datas['thumb'];
    }

    /**
     * 链接
     * @param  [type]
     * @return [type]
     */
    protected function _getUrl($datas)
    {
        $thumb = $this->_getImage($datas);
        return serialize([
            'title' => $datas['url']['title'],
            'content' => $datas['url']['desc'],
            'url' => $datas['url']['url'],
            'thumb' => $thumb,
            'thumb_url' => $thumb
        ]);
    }

    /**
     * @param $data
     * @return string
     */
    protected function _getGroup($data)
    {
        $groupId = (explode(',',$data));
        $groupIds = [];
        foreach($groupId as $item){
            $groupIds[] = RobotGroup::find($item)->robot_group_id;
        }
        return implode(',',$groupIds);
    }
    /**
     * @param $status
     * @param $message
     * @param mixed $data
     *
     * 业务逻辑统一结果输出
     */
    protected static function getResult($status, $message, $data = null)
    {
        if (!is_null($data)) {
            return ['status' => $status, 'message' => $message, 'data' => $data];
        }
        return ['status' => $status, 'message' => $message];
    }


    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Robot\Events\RobotKeywordSaveEvent',
            'Robot\Listeners\RobotKeywordListener@onSave');
    }
}
