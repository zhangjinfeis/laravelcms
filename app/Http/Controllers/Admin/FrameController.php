<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;

class FrameController extends Controller
{
    /**
     * 后台框架
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * Created by zjf
     * Time: 2018/11/7 10:41
     */
    public function index(Request $request){
        /*$hello = 'hello world';

        $article = Article::find(6);
        $body = htmlspecialchars_decode($article->body);

        preg_match_all('/<img\b.*src=.*([0-9a-z]{32})\./i', $body, $res);
        dd($res);*/




        return view('admin.frame.index');
    }
}
