<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pic;

class ProcessPic
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
            Pic::processPic($request->pic_not_use_id,$request->pic_use_id,$request->editor_not_use,$request->editor_use);
        }
        return $next($request);
    }
}
