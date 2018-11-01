<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Link;
use App\Models\Guestbook;
use App\Models\Map;
use App\Models\ManagerUser;

class IndexController extends Controller
{

    public function index(Request $request){

        //文章数
        $article_count = Article::count();
        $sign['article_count'] = $article_count;
        //链接数
        $link_count = Link::count();
        $sign['link_count'] = $link_count;
        //留言数
        $guestbook_count = Guestbook::count();
        $sign['guestbook_count'] = $guestbook_count;
        //地图数
        $map_count = Map::count();
        $sign['map_count'] = $map_count;
        //管理员数
        $manager_count = ManagerUser::count();
        $sign['manager_count'] = $manager_count;

        //近一周文章发布情况
        $labels = [];
        for($i=6;$i>=0;$i--){
            array_push($labels,date('Y-m-d',time()-$i*24*3600));
        }
        $sign['labels'] = $labels;
        $data = [];
        foreach($labels as $vo){
            $count = Article::whereBetween('created_at',[strtotime($vo),strtotime($vo.' 23:59:59')])->count();
            array_push($data,$count);
        }
        $sign['data'] = $data;
        //最近发布的文章
        $article = Article::orderBy('id','desc')->limit(6)->get();
        $sign['article'] = $article;
        return view('admin.index.index',$sign);
    }
}
