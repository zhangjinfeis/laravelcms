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
     * 处理图片状态的方法
     * @author my  2017-11-3
     * @param $not_use 不使用的图片md5数组
     * @param $use 要使用的图片的md5
     * @param $not_use_html 匹配html中的不使用图片的md5
     * @param $use_html 匹配html中的要使用图片的md5
     * @return bool 成功or失败
     */
    protected function processFile($not_use=[],$use=[]){
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

}
