<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pic;
use App\Models\File;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

/**
 * 文件上传与下载管理
 * @author my 2017-10-25
 * Class AreaController
 * @package App\Http\Controllers\Admin
 */
class UploadController extends Controller
{
    private $config; //基本配置

    public function __construct()
    {
        $this->config = [
            //'need_original_name_as_path'    => false,
            'allowed_ext_pic'               => ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
            'allowed_ext_file'              => [
                'png', 'jpg', 'jpeg', 'gif', 'bmp',
                'flv', 'swf', 'mkv', 'avi', 'rm', 'rmvb', 'mpeg', 'mpg',
                'ogg', 'ogv', 'mov', 'wmv', 'mp4', 'webm', 'mp3', 'wav', 'mid',
                'rar', 'zip', 'tar', 'gz', '7z', 'bz2', 'cab', 'iso', 
                'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt', 'md', 'xml'
            ],
            'max_allowed_size_pic'  => 0, // 0-表示不限制 单位Mb
            'max_allowed_size_file' => 0,
            //'upload_root_path'      => storage_path() . '/storage/uploads/',
        ];
    }


    /**
     * 上传图片
     * @author my  2017-10-26
     * @param Request $request
     * @return array
     */
    public function ajaxUploadImg(Request $request){
        //获取文件
        $image = $request->file('file');
        //校验文件
        if($image->getError() > 0){//文件传输有错误
            switch ($image->getError()){
                case 1:
                    return ["status"=>0,"msg"=>"图片上传失败，图片大小不能超出".ini_get('upload_max_filesize')];
                    break;
                case 2:
                    return ["status"=>0,"msg"=>"图片上传失败，图片大小不能超出表单的提交限制"];
                    break;
                case 3:
                    return ["status"=>0,"msg"=>"图片上传失败，请检查网络状态是否可用"];
                    break;
                case 4:
                    return ["status"=>0,"msg"=>"图片上传失败，请检查图片文件完整性"];
                    break;

            }
        }else if(!in_array($image->getClientOriginalExtension(),$this->config['allowed_ext_pic'])){
            //判断文件类型
            return ["status"=>0,"msg"=>"上传图片失败，请上传正确的图片格式文件"];
        }

        //存储文件
        $md5 = md5(time().str_random(40).env("md5_key",""));
        $filename = $md5.".".$image->getClientOriginalExtension();//新文件名
        $filepath = 'picture/'.date("Y").date("m")."/".date("d");
        $path = $image->storeAs('public'.'/'.$filepath,$filename);  //起始路径为storage/app

        /**
         * 压缩图片
         */
        $manager  = new ImageManager();
        $image_new = $manager ->make('../storage/app/public/'.$filepath.'/'.$filename)->orientate();
        if($request->width && $request->height){
            $image_new = $image_new->fit($request->width,$request->height);
        }elseif($request->width){
            $image_new = $image_new->widen($request->width,function($constraint){
                $constraint->upsize();
            });
        }elseif($request->height){
            $image_new = $image_new->heighten($request->height,function($constraint){
                $constraint->upsize();
            });
        }
        $image_new->save('../storage/app/public/'.$filepath.'/'.$filename);


        //修改数据库记录
        $param['original_name'] = $image->getClientOriginalName();
        $param['name'] = $filename;
        $param['path'] = "/storage/".$filepath.'/'.$filename;
        $param['md5'] = $md5;
        $param['sha1'] = sha1($md5.env("sha1_key"));
        $param['width'] = $image_new->width();
        $param['height'] = $image_new->height();
        $param['size'] = $image_new->filesize();//获得文件的大小（字节）
        $param['ext'] = $image->getClientOriginalExtension();//获得文件的后缀
        $pic = Pic::create($param);//创建一条图片记录
        //返回信息
        return ["status"=>1,"msg"=>"","data"=>$pic];
    }


    /**
     * 上传商品图片
     * @author my  2017-10-26
     * @param Request $request
     * @return array
     */
    public function ajaxUploadImgsGoods(Request $request){
        //获取文件
        $image = $request->file('file');
        //校验文件
        if($image->getError() > 0){//文件传输有错误
            switch ($image->getError()){
                case 1:
                    return ["status"=>0,"msg"=>"图片上传失败，图片大小不能超出".ini_get('upload_max_filesize')];
                    break;
                case 2:
                    return ["status"=>0,"msg"=>"图片上传失败，图片大小不能超出表单的提交限制"];
                    break;
                case 3:
                    return ["status"=>0,"msg"=>"图片上传失败，请检查网络状态是否可用"];
                    break;
                case 4:
                    return ["status"=>0,"msg"=>"图片上传失败，请检查图片文件完整性"];
                    break;

            }
        }else if(!in_array($image->getClientOriginalExtension(),$this->config['allowed_ext_pic'])){
            //判断文件类型
            return ["status"=>0,"msg"=>"上传图片失败，请上传正确的图片格式文件"];
        }

        //存储文件
        $md5 = md5(time().str_random(40).env("md5_key",""));
        $filename = $md5.".".$image->getClientOriginalExtension();//新文件名
        $filename_mid = $md5."_mid.".$image->clientExtension();//新文件名
        $filename_min = $md5."_min.".$image->clientExtension();//新文件名
        $filepath = 'goods/'.date("Y").date("m")."/".date("d");
        $path = $image->storeAs('public'.'/'.$filepath,$filename);  //起始路径为storage/app

        /**
         * 压缩图片
         */
        $request->size = explode(',',$request->size);
        $request->size_mid = explode(',',$request->size_mid);
        $request->size_min = explode(',',$request->size_min);

        $manager  = new ImageManager();
        $image_new = $manager ->make('../storage/app/public/'.$filepath.'/'.$filename)->orientate();
        //大图
        $image_new = $image_new->fit($request->size[0],$request->size[1]);
        $image_new->save('../storage/app/public/'.$filepath.'/'.$filename);
        //中等大小
        $image_new = $image_new->fit($request->size_mid[0],$request->size_mid[1]);
        $image_new->save('../storage/app/public/'.$filepath.'/'.$filename_mid);
        //小图
        $image_new = $image_new->fit($request->size_min[0],$request->size_min[1]);
        $image_new->save('../storage/app/public/'.$filepath.'/'.$filename_min);


        //修改数据库记录
        $param['original_name'] = $image->getClientOriginalName();
        $param['name'] = $filename;
        $param['path'] = "/storage/".$filepath.'/'.$filename;
        $param['md5'] = $md5;
        $param['sha1'] = sha1($md5.env("sha1_key"));
        $param['width'] = $image_new->width();
        $param['height'] = $image_new->height();
        $param['size'] = $image_new->filesize();//获得文件的大小（字节）
        $param['ext'] = $image->getClientOriginalExtension();//获得文件的后缀
        $pic = Pic::create($param);//创建一条图片记录
        //返回信息
        return ["status"=>1,"msg"=>"","data"=>$pic];
    }


    /**
     * ckeditor上传图片
     * @author my  2017-11-3
     * @param Request $request
     * @return array
     */
    public function ajaxCkeditorImg(Request $request){
        $image = $request->file('upload');
        $callback = $_REQUEST["CKEditorFuncNum"];
        $error = "";
        //校验文件
        if($image->getError() > 0){//文件传输有错误
            switch ($image->getError()){
                case 1:
                    $error = "图片上传失败，图片大小不能超出".ini_get('upload_max_filesize');
                    break;
                case 2:
                    $error = "图片上传失败，图片大小不能超出表单的提交限制";

                    break;
                case 3:
                    $error = "图片上传失败，请检查网络状态是否可用";
                    break;
                case 4:
                    $error = "图片上传失败，请检查图片文件完整性";
                    break;
            }
            if(!empty($error)){
                return "<script>window.parent.CKEDITOR.tools.callFunction($callback, '', '$error');</script>";
            }

        }else if(!in_array($image->getClientOriginalExtension(),$this->config['allowed_ext_pic'])){
            //判断文件类型
            $error = "上传图片失败，请上传正确的图片格式文件";
            return "<script>window.parent.CKEDITOR.tools.callFunction($callback, '', '$error');</script>";
        }



        //存储文件
        $md5 = md5(time().str_random(40).env("md5_key"));
        $filename = $md5.".".$image->getClientOriginalExtension();//新文件名
        $filepath = date("Y").date("m")."/".date("d");
        $path = $image->storeAs('public'.'/'.$filepath,$filename);  //起始路径为storage/app

        /**
         * 压缩图片
         */
        $manager  = new ImageManager();
        $image_new = $manager ->make('../storage/app/public/'.$filepath.'/'.$filename)->orientate();
        if($request->width && $request->height){
            $image_new = $image_new->fit($request->width,$request->height);
        }elseif($request->width){
            $image_new = $image_new->widen($request->width,function($constraint){
                $constraint->upsize();
            });
        }elseif($request->height){
            $image_new = $image_new->heighten($request->height,function($constraint){
                $constraint->upsize();
            });
        }
        $image_new->save('../storage/app/public/'.$filepath.'/'.$filename);

        //修改数据库记录
        $param['original_name'] = $image->getClientOriginalName();
        $param['name'] = $filename;
        $param['path'] = "/storage/".$filepath.'/'.$filename;
        $param['md5'] = $md5;
        $param['sha1'] = sha1($md5.env("sha1_key"));
        $param['width'] = $image_new->width();
        $param['height'] = $image_new->height();
        $param['size'] = $image_new->filesize();//获得文件的大小（字节）
        $param['ext'] = $image->getClientOriginalExtension();//获得文件的后缀
        $pic = Pic::create($param);//创建一条图片记录
        //返回信息

        $previewname = $param['path'];
        return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'".$previewname."','');</script>";
    }

    /**
     * 上传单文件
     * @author kevin  2017-11-01
     */
    public function ajaxUploadFile(Request $request){
        //获取文件
        $file = $request->file('file');
        //校验文件
        if($file->getError() > 0){//文件传输有错误
            switch ($file->getError()){
                case 1:
                    return ["status"=>0,"msg"=>"文件上传失败，文件大小不能超出".ini_get('upload_max_filesize')];
                    break;
                case 2:
                    return ["status"=>0,"msg"=>"文件上传失败，文件大小不能超出表单的提交限制"];
                    break;
                case 3:
                    return ["status"=>0,"msg"=>"文件上传失败，请检查网络状态是否可用"];
                    break;
                case 4:
                    return ["status"=>0,"msg"=>"文件上传失败，请检查图片文件完整性"];
                    break;

            }
        }else if(!in_array($file->getClientOriginalExtension(),$this->config['allowed_ext_file'])){
            //判断文件类型
            return ["status"=>0,"msg"=>"上传文件失败，请上传正确格式的文件"];
        }

        //存储文件
        $md5 = md5(time().str_random(40).env("md5_key",""));
        $filename = $md5.".".$file->getClientOriginalExtension();//新文件名
        $filepath = 'file/'.date("Y").date("m")."/".date("d");
        $path = $file->storeAs('public'.'/'.$filepath,$filename);  //起始路径为storage/app

        //修改数据库记录
        $param['original_name'] = $file->getClientOriginalName();
        $param['name'] = $filename;
        $param['path'] = "/storage/".$filepath.'/'.$filename;
        $param['md5'] = $md5;
        $param['sha1'] = sha1($md5.env("sha1_key"));
        $param['size'] = $file->getClientSize();//获得文件的大小（字节）
        $param['ext'] = $file->getClientOriginalExtension();//获得文件的后缀
        $res = File::create($param);//创建一条图片记录
        $data['size'] = format_bytes($res->size);
        $data['original_name'] = $res->original_name;
        $data['ext'] = $res->ext;
        $data['path'] = $res->path;
        $data['md5'] = $res->md5;
        //返回信息
        return ["status"=>1,"msg"=>"上传成功","data"=>$data];

    }



}