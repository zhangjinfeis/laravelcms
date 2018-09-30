<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;

class IndexController extends Controller
{

    public function index(Request $request){

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
