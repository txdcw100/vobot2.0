<?php
/**
 * Created by PhpStorm.
 * User: maczheng
 * Date: 2020-03-10
 * Time: 09:47
 */

namespace Robot\Models;


use App\Models\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RobotGroupMessage extends Model
{
    protected $guarded = [];

    protected $appends = ['format_content'];
    /**
     * 群
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groups()
    {
        return $this->belongsTo(RobotGroup::class, 'group_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * 群成员
     */
    public function friend()
    {
        return $this->belongsTo(RobotGroupFriend::class, 'wx_id', 'wx_id');
    }

    public static function getAllTypes()
    {
        return [1=>'文本', 3=>'图片', 34 => '音频', 43 => '视频', 49 => '链接'];
    }

    /**
     * @return mixed|string
     */
    public function getFormatContentAttribute()
    {
        switch ($this->msg_type){
            case 3:
            case 47:
            return '<img src="' . $this->content.'" height="30">';
            default:
                return $this->content;
                break;
        }
    }

    /**
     * 自动创建表
     */
    public function SchemaTable()
    {
        $curTable = $this->getTable().now()->format('Ym');
        Cache::rememberForever($curTable, function() use($curTable){
            $tables = DB::select('SHOW TABLES');
            $object = json_decode(json_encode($tables),true);
            $tbNames = [];
            $database = config('database.connections.mysql.database');
            array_walk($object, function($item) use(&$tbNames, $database){
                $tbNames[] = $item['Tables_in_'.$database];
            });
            $isin = in_array($curTable, $tbNames);
            if(!$isin){
                Schema::create($curTable, function($table){
                    $table->bigIncrements('id');
                    $table->integer('friend_id')->default(0)->comment('群成员ID');
                    $table->integer('group_id')->index()->default(0)->comment('群ID');
                    $table->char('wx_id',50)->index()->nullable(0)->comment('微信号');
                    $table->char('msg_type', 10)->index()->nullable(0)->comment('消息类型');
                    $table->char('msg_id', 30)->nullable(0)->comment('robot 消息ID');
                    $table->text('content')->nullable(0)->charset('utf8mb4')->collation('utf8mb4_general_ci')->comment('内容');
                    $table->timestamps();
                });
                DB::statement("ALTER TABLE {$curTable} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            }
        });
        return $curTable;
    }

}
