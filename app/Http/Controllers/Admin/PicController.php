<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pic;

/**
 * 图片资源
 * Created by zjf
 * Time: 2018/11/2 17:07
 */
class PicController extends Controller
{

    /**
     * 图片列表
     * @author my  2017-10-25
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){

        $list = Pic::when($request->is_used,function($query)use($request){
            return $query->where('is_used',$request->is_used);
        });
        $list = $list->orderBy('id','desc')->paginate(210);
        foreach($list as &$vo){
            $vo->substr_name = mb_substr($vo->name,0,5).'...'.mb_substr($vo->name,-8);
        }
        $sign['list'] = $list;
        return view('admin/pic/index', $sign);
    }


    /**
     * 图片详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Created by zjf
     * Time: 2018/11/2 22:50
     */
    public function ajax_detail(Request $request){
        $pic = Pic::where('md5',$request->md5)->first()->toArray();
        $pic['size'] = format_bytes($pic['size']);
        $pic['px'] = $pic['width'].'*'.$pic['height'].'px';
        $pic['created_at'] = date('Y-m-d H:i:s',$pic['created_at']);
        return response()->json(['status'=>1,'msg'=>'获取成功','data'=>$pic]);
    }

    /**
     * 清除过期图片
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Created by zjf
     * Time: 2018/11/2 23:13
     */
    public function ajax_clear(Request $request){
        Pic::where('is_used',9)->where('created_at','<',time()-24*3600)->chunk(100,function($pics){
            $ids = [];
            foreach($pics as $vo){
                array_push($ids,$vo->id);
                if(file_exists('.'.$vo->path)){
                    unlink('.'.$vo->path);
                }
                if(file_exists('.'.img_rename($vo->path,'_mid'))){
                    unlink('.'.img_rename($vo->path,'_mid'));
                }
                if(file_exists('.'.img_rename($vo->path,'_min'))){
                    unlink('.'.img_rename($vo->path,'_min'));
                }
            }
            Pic::whereIn('id',$ids)->delete();
        });
        return response()->json(['status'=>1,'msg'=>'清除成功']);
    }


}