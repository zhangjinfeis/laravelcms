<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pic;
use App\Models\File;

class ProcessPicFile
{
    /**
     * 处理提交过程中的图片状态
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->isMethod('post')){
            //图片有效性处理
            Pic::processPic($request->pic_not_use_id,$request->pic_use_id,$request->editor_not_use,$request->editor_use);
            unset($request['pic_not_use_id'],$request['pic_use_id'],$request['editor_not_use'],$request['editor_use']);

            //文件有效性处理
            File::processFile($request->file_not_use_id,$request->file_use_id);
            unset($request['file_not_use_id'],$request['file_use_id']);
        }
        return $next($request);
    }
}
