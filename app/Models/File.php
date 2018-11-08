<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 文件模型
 * Created by zjf
 * Time: 2018/11/6 15:02
 */
class File extends Model
{
    protected $table = 'file';
    protected $guarded = [];
    protected $dateFormat = 'U';


    /**
     * 文件状态更新
     * @param $request
     * @return bool
     * Created by zjf
     * Time: 2018/11/8 15:05
     */
    protected function update_is_used($request){
        $not_use = isset($request->file_not_use_id)?$request->file_not_use_id:[];
        $use = isset($request->file_use_id)?$request->file_use_id:[];
        $file_not_use_id=[];
        if($not_use){
            foreach ($not_use as $n){
                $n = explode(',',$n);
                $file_not_use_id = array_merge($file_not_use_id,$n);
            }
            $file_not_use_id = array_unique($file_not_use_id);
        }

        $file_use_id = [];
        if($use){
            foreach ($use as $u){
                $u = explode(',',$u);
                $file_use_id = array_merge($file_use_id,$u);
            }
            $file_use_id = array_unique($file_use_id);
        }

        SELF::whereIn('md5',$file_not_use_id)->update(['is_used' => 9]);
        SELF::whereIn('md5',$file_use_id)->update(['is_used' => 1]);
        return true;
    }

    /**
     * 清除指定文件资源
     * @param $table 表名
     * @param array $ids 记录id
     * @param array $file_keys 处理的字段
     * Created by zjf
     * Time: 2018/11/8 15:13
     */
    protected function clearContent($table,$ids=[],$file_keys=[]){
        $file_not_use_id = [];
        $file_str = '';
        $list = DB::table($table)->whereIn('id',$ids)->get();
        $list = json_decode(json_encode($list),true);
        foreach($list as $val){
            //拼接图片字段
            if(count($file_keys)>0){
                for($i = 0;$i < count($file_keys);$i++){
                    $file_str.='@'.$val[$file_keys[$i]];
                }
            }
        }
        //匹配并合并md5
        if(count($file_keys)>0){
            preg_match_all('/([0-9a-z]{32})\./i', $file_str, $res_pic);
            if($res_pic){
                $pic_not_use_id = array_merge($file_not_use_id,$res_pic[1]);
            };
        }
        SELF::whereIn('md5',$pic_not_use_id)->update(['is_used' => 9]);
    }

}
