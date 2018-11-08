<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Article;
use App\Models\Pic;
use Validator;

/**
 * 首页模块
 * Created by zjf
 * Time: 2018/9/28 0:02
 */
class IndexController extends Controller
{

    /**
     * 首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * Created by zjf
     * Time: 2018/9/28 0:10
     */
    public function index(Request $request){
        $article = Article::find(6);
        $sign['article'] = $article;
        return view('home.index.index',$sign);
    }


}