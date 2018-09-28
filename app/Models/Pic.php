<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
 * 图片模型
 * @author my 2017-10-26
 * Class Pic
 * @package App
 */
class Pic extends Model
{
    protected $table = 'pic';
    protected $guarded = [];

    protected $dateFormat = 'U';

    /**
     * 获取用于图片上传
     * @author zjf 2017-11-03
     * @param $id
     * @return mixed
     */
    protected function getUploadById($id){
        return $this->select('width','height','size','md5')->where('id',$id)->first();
    }

    /**
     * 获取用于图片上传的图片组
     * @author zjf 2017-11-03
     * @param $ids
     * @return mixed
     */
    protected function getUploadByIds($ids){
        $ids = explode(',',$ids);
        return $this->select('width','height','size','md5')->whereIn('id',$ids)->get();
    }

    /**
     * 获取用于图片上传
     * @author zjf 2017-11-07
     * @param $md5
     * @return mixed
     */
    protected function getUploadByMd5($md5){
        return $this->select('width','height','size','md5')->where('md5',$md5)->first();
    }

    /**
     * 获取用于图片上传
     * @author zjf 2017-11-07
     * @param $md5s
     * @return mixed
     */
    protected function getUploadByMd5s($md5s){
        $md5s = explode(',',$md5s);
        return $this->select('width','height','size','md5')->whereIn('md5',$md5s)->first();
    }

    /**
     * 处理图片状态的方法
     * @author my  2017-11-3
     * @param $not_use 不使用的图片md5数组
     * @param $use 要使用的图片的md5
     * @param $not_use_html 匹配html中的不使用图片的md5
     * @param $use_html 匹配html中的要使用图片的md5
     * @return bool 成功or失败
     */
    protected function processPic($not_use=[],$use=[],$not_use_html=[],$use_html=[]){
        $pic_not_use_id=[];
        if($not_use){
            foreach ($not_use as $n){
                $n = explode(',',$n);
                $pic_not_use_id = array_merge($pic_not_use_id,$n);
            }
            $pic_not_use_id = array_unique($pic_not_use_id);
        }

        $pic_use_id = [];
        if($use){
            foreach ($use as $u){
                $u = explode(',',$u);
                $pic_use_id = array_merge($pic_use_id,$u);
            }
            $pic_use_id = array_unique($pic_use_id);
        }

        if(!empty($not_use_html)){
            //展开成字符串
            $not_use_html = implode('@@',$not_use_html);
            preg_match_all('/[0-9a-z]{32}/i', $not_use_html, $res);
            if($res){
                $pic_not_use_id = array_merge($pic_not_use_id,$res[0]);
            }

        }

        if(!empty($use_html)){
            //展开成字符串
            $use_html = implode('@@',$use_html);
            preg_match_all('/[0-9a-z]{32}/i', $use_html, $res);
            if($res){
                $pic_use_id = array_merge($pic_use_id,$res[0]);
            };
        }
        SELF::whereIn('md5',$pic_not_use_id)->update(['is_used' => 0]);
        SELF::whereIn('md5',$pic_use_id)->update(['is_used' => 1]);
        return true;
    }

}
