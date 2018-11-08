<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    /*protected function getUploadById($id){
        return $this->select('width','height','size','md5')->where('id',$id)->first();
    }*/

    /**
     * 获取用于图片上传的图片组
     * @author zjf 2017-11-03
     * @param $ids
     * @return mixed
     */
    /*protected function getUploadByIds($ids){
        $ids = explode(',',$ids);
        return $this->select('width','height','size','md5')->whereIn('id',$ids)->get();
    }*/

    /**
     * 获取用于图片上传
     * @author zjf 2017-11-07
     * @param $md5
     * @return mixed
     */
    /*protected function getUploadByMd5($md5){
        return $this->select('width','height','size','md5')->where('md5',$md5)->first();
    }*/

    /**
     * 获取用于图片上传
     * @author zjf 2017-11-07
     * @param $md5s
     * @return mixed
     */
    /*protected function getUploadByMd5s($md5s){
        $md5s = explode(',',$md5s);
        return $this->select('width','height','size','md5')->whereIn('md5',$md5s)->first();
    }*/

    /**
     * 新增、编辑后图片状态更新
     * @param $request
     * @return bool
     * Created by zjf
     * Time: 2018/11/8 15:02
     */
    protected function update_is_used($request){
        $not_use = isset($request->pic_not_use_id)?$request->pic_not_use_id:[];
        $use = isset($request->pic_use_id)?$request->pic_use_id:[];
        $not_use_html = isset($request->editor_not_use)?$request->editor_not_use:[];
        $use_html = isset($request->editor_use)?$request->editor_use:[];

        $pic_not_use_id=[];
        foreach ($not_use as $n){
            $n = explode(',',$n);
            $pic_not_use_id = array_merge($pic_not_use_id,$n);
        }
        $pic_not_use_id = array_unique($pic_not_use_id);

        $pic_use_id = [];
        foreach ($use as $u){
            $u = explode(',',$u);
            $pic_use_id = array_merge($pic_use_id,$u);
        }
        $pic_use_id = array_unique($pic_use_id);

        if(count($not_use_html)>0){
            //展开成字符串
            $not_use_html = implode('@',$not_use_html);
            $not_use_html = htmlspecialchars_decode($not_use_html);
            preg_match_all('/<img\b[^>]*src="[\/\w\d]+([0-9a-z]{32})\./i', $not_use_html, $res);
            if($res){
                $pic_not_use_id = array_merge($pic_not_use_id,$res[1]);
            }

        }
        if(count($use_html)>0){
            //展开成字符串
            $use_html = implode('@',$use_html);
            $use_html = htmlspecialchars_decode($use_html);
            preg_match_all('/<img\b[^>]*src="[\/\w\d]+([0-9a-z]{32})\./i', $use_html, $res);
            if($res){
                $pic_use_id = array_merge($pic_use_id,$res[1]);
            };
        }

        SELF::whereIn('md5',$pic_not_use_id)->update(['is_used' => 9]);
        SELF::whereIn('md5',$pic_use_id)->update(['is_used' => 1]);
        return true;
    }


    /**
     * 清除对应的图片资源
     * @param $table  操作表
     * @param $ids 操作ids
     * @param $pics 图片字段
     * @param $html 带图片的html的字段
     * @return bool
     * Created by zjf
     * Time: 2018/11/5 18:08
     */
    protected function clearContent($table,$ids=[],$pic_keys=[],$html_keys=[],$json_keys=[]){
        $pic_not_use_id = [];
        $pic_str = '';
        $html_str = '';
        $json_str = '';
        $list = DB::table($table)->whereIn('id',$ids)->get();
        $list = json_decode(json_encode($list),true);
        foreach($list as $val){
            //拼接图片字段
            if(count($pic_keys)>0){
                for($i = 0;$i < count($pic_keys);$i++){
                    $pic_str.='@'.$val[$pic_keys[$i]];
                }
            }
            //拼接编辑器字段
            if(count($html_keys)>0){
                for($i = 0;$i < count($html_keys);$i++){
                    $html_str.='@'.htmlspecialchars_decode($val[$html_keys[$i]]);
                }
            }
            //拼接json字段
            if(count($json_keys)>0){
                for($i = 0;$i < count($json_keys);$i++){
                    $json_str.='@'.$val[$json_keys[$i]];
                }
            }
        }
        //匹配并合并md5
        if(count($pic_keys)>0){
            preg_match_all('/([0-9a-z]{32})\./i', $pic_str, $res_pic);
            if($res_pic){
                $pic_not_use_id = array_merge($pic_not_use_id,$res_pic[1]);
            };
        }
        //匹配并合并md5
        if(count($html_keys)>0){
            preg_match_all('/<img\b[^>]*src="[\/\w\d]+([0-9a-z]{32})\./i', $html_str, $res_html);
            if($res_html){
                $pic_not_use_id = array_merge($pic_not_use_id,$res_html[1]);
            };
        }
        //匹配并合并md5
        if(count($json_keys)>0){
            preg_match_all('/([0-9a-z]{32})\./i', $json_str, $res_json);
            if($res_json){
                $pic_not_use_id = array_merge($pic_not_use_id,$res_json[1]);
            };
        }
        SELF::whereIn('md5',$pic_not_use_id)->update(['is_used' => 9]);
    }

}
