<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\File;

/**
 * 文件资源
 * Created by zjf
 * Time: 2018/11/2 17:07
 */
class FileController extends Controller
{

    /**
     * 文件列表
     * @author my  2017-10-25
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){

        $list = File::orderBy('id','desc')->paginate(80);
        foreach($list as &$vo){
            $vo->substr_name = mb_substr($vo->name,0,5).'...'.mb_substr($vo->name,-8);
        }
        $sign['list'] = $list;
        return view('admin/file/index', $sign);
    }


    /**
     * 文件详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Created by zjf
     * Time: 2018/11/2 22:50
     */
    public function ajax_detail(Request $request){
        $file = File::where('md5',$request->md5)->first()->toArray();
        $file['size'] = format_bytes($file['size']);
        $file['is_used'] = $file['is_used']==9?"否":"是";
        $file['created_at'] = date('Y-m-d H:i:s',$file['created_at']);
        return response()->json(['status'=>1,'msg'=>'获取成功','data'=>$file]);
    }

    /**
     * 清除过期文件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Created by zjf
     * Time: 2018/11/2 23:13
     */
    public function ajax_clear(Request $request){
        File::where('is_used',9)->where('created_at','<',time()-24*3600)->chunk(100,function($files){
            $ids = [];
            foreach($files as $vo){
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
            File::whereIn('id',$ids)->delete();
        });
        return response()->json(['status'=>1,'msg'=>'清除成功']);
    }


}