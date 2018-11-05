<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use App\Models\ArticleCate;
use App\Models\Article;
use App\Models\ArticleExattr;
use App\Models\Pic;
use Illuminate\Support\Facades\DB;

/**
 * 后台菜单控制器
 * @author my 2017-10-25
 * Class MenuController
 * @package App\Http\Controllers\Admin
 */
class ArticleController extends Controller
{

    /**
     * 菜单列表
     * @author my  2017-10-25
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){
        //条件筛选
        $list = Article::when($request->title,function($query)use($request){
            return $query->where('title','like','%'.$request->title.'%');
        })->when($request->is_show,function($query)use($request){
            return $query->where('is_show',$request->is_show);
        })->when($request->cate_id,function($query)use($request){
            return $query->where('cate_id',$request->cate_id);
        });
        $list= $list->orderBy('sort','asc')->orderBy('updated_at','desc')->paginate(15);
        $sign['list']=$list;

        //文章分类
        $cate = ArticleCate::getList();
        $sign['cate'] = $cate;

        //总记录数
        $sign['count'] = $list->count();

        return view('admin/article/index', $sign);
    }

    /**
     * 创建文章
     * @author my  2017-10-25
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request){
        if($request->isMethod('post')){
            //dd($request->all());
            $rule = [
                'title' => 'required|between:1,100',
            ];

            $request->title_sub && $rule['title_sub'] ='between:1,100';
            $request->keywords && $rule['keywords'] ='between:1,100';
            $request->description && $rule['description'] ='between:1,500';

            $message = [
                'required' => ':attribute不能为空',
                'title.between' => ':attribute字符长度1-100',
                'title_sub.between' => ':attribute字符长度1-100',
                'keywords.between' => ':attribute字符长度1-100',
                'description.between' => ':attribute字符长度1-500',
            ];
            $replace = [
                'title' => '标题',
                'title_sub' => '副标题',
                'keywords' => '关键词',
                'description' => '描述',
            ];

            $validator = Validator::make($request->all(),$rule,$message,$replace);
            if ($validator->fails()){
                $a = $validator->errors()->toArray();

                foreach($a as $k => $v){
                    $data['field'] = $k;
                    $data['msg'] = $v[0];
                    break;
                }
                return response()->json(['status'=>0,'msg'=>$data['msg'],'field'=>$data['field']]);
            }
            //新增
             if($request->created_at!=''){
                $article['created_at'] = strtotime($request->created_at);
            }
            $article['title'] = $request->title;
            $article['title_sub'] = $request->title_sub;
            $article['cate_id'] = $request->cate_id;
            $article['url'] = $request->url;
            $article['thumb'] = $request->thumb;
            $article['thumbs'] = $request->thumbs;
            $article['body'] = htmlspecialchars($request->body);
            $article['keywords'] = $request->keywords;
            $article['description'] = $request->description;
            $article['is_show'] = $request->is_show;
            $article['exattr'] = json_encode($request->exattr);
            //dd($article);
            $article = Article::create($article);
            if(!$article){
                return response()->json(['status'=>0,'msg'=>'新增失败']);
            }else{
                return response()->json(['status'=>1,'msg'=>'新增成功']);
            }
        }else{
            //载入文章分类
            $sign['cate'] = ArticleCate::getList();
            return view('admin.article.create',$sign);
        }
    }

    /**
     * 删除文章
     * @author my  2017-10-25
     * @param Request $request 请求
     * @return array
     */
    public function ajaxDel(Request $request){
        Pic::clearContent('article',$request->ids);
        $res = Article::whereIn('id',$request->ids)->delete();
        if($res){
            return ['status'=>1,'msg'=>'删除成功'];
        }else {
            return ['status'=>0,'msg'=>'删除失败'];
        }
    }

    /**
     * 移动文章到某个分类下
     * @author my  2017-10-25
     * @param Request $request 请求
     * @return array
     */
    public function ajaxMove(Request $request){
        $res = Article::whereIn('id',$request->ids)->update(['cate_id'=>$request->move_to_id]);
        if($res){
            return ['status'=>1,'msg'=>'移动成功'];
        }else {
            return ['status'=>0,'msg'=>'移动失败'];
        }
    }

    /**
     * 文章编辑
     * @author my  2017-10-25
     * @param Request $request
     * @param ArticleCate $menu 依赖注入的菜单模型
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request){
        if($request->isMethod('post')){
            //dd($request->all());

            $rule = [
                'title' => 'required|between:1,100',
            ];

            $request->title_sub && $rule['title_sub'] ='between:1,100';
            $request->keywords && $rule['keywords'] ='between:1,100';
            $request->description && $rule['description'] ='between:1,500';

            $message = [
                'required' => ':attribute不能为空',
                'title.between' => ':attribute字符长度1-100',
                'title_sub.between' => ':attribute字符长度1-100',
                'keywords.between' => ':attribute字符长度1-100',
                'description.between' => ':attribute字符长度1-500',
            ];
            $replace = [
                'title' => '标题',
                'title_sub' => '副标题',
                'keywords' => '关键词',
                'description' => '描述',
            ];

            $validator = Validator::make($request->all(),$rule,$message,$replace);
            if ($validator->fails()){
                $a = $validator->errors()->toArray();

                foreach($a as $k => $v){
                    $data['field'] = $k;
                    $data['msg'] = $v[0];
                    break;
                }
                return response()->json(['status'=>0,'msg'=>$data['msg'],'field'=>$data['field']]);
            }
            //更新
             if($request->created_at!=''){
                $article['created_at'] = strtotime($request->created_at);
            }
            $article['title'] = $request->title;
            $article['title_sub'] = $request->title_sub;
            $article['cate_id'] = $request->cate_id;
            $article['url'] = $request->url;
            $article['thumb'] = $request->thumb;
            $article['thumbs'] = $request->thumbs;
            $article['body'] = htmlspecialchars($request->body);
            $article['keywords'] = $request->keywords;
            $article['description'] = $request->description;
            $article['is_show'] = $request->is_show;
            $article['exattr'] = json_encode($request->exattr);
            $article = Article::where('id',$request->id)->update($article);
            if(!$article){
                return response()->json(['status'=>0,'msg'=>'编辑失败']);
            }else{
                return response()->json(['status'=>1,'msg'=>'编辑成功']);
            }
        }else{
            //载入文章
            $article = Article::find($request->id);

            $sign['article'] = $article;
            //载入文章分类
            $sign['cate'] = ArticleCate::getList();
            return view('admin.article.edit',$sign);
        }
    }

    /**
     * 返回附加值html
     * @author zjf ${date}
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxExattr(Request $request){
        $exattr = ArticleExattr::where('cate_id',$request->cate_id)->orderBy('sort','asc')->orderBy('id','asc')->get()->toArray();
        if(count($exattr) == 0){
            $cate = ArticleCate::where('id',$request->cate_id)->first();
            if($cate && $cate->parent_id){
                $exattr = ArticleExattr::where('cate_id',$cate->parent_id)->orderBy('sort','asc')->orderBy('id','asc')->get()->toArray();
            }
        }
        foreach($exattr as $ke => $val){
            if(!empty($val['radio_checkbox_json'])){

                $item = str_replace("\r\n","\n",$exattr[$ke]['radio_checkbox_json']);
                $item = trim($item,"\n");
                $item = explode("\n",$item);
                foreach($item as $k => $v){
                    $item[$k] = explode('-',$v);
                }
                $exattr[$ke]['radio_checkbox_json'] = $item;
            }
        }

        if($request->article_id){
            $article = Article::find($request->article_id);
            $exattr_val = json_decode($article->exattr,true);
            foreach($exattr as $key=>$value){
                if($exattr_val){
                    foreach($exattr_val as $k=>$v){
                        if($value['key'] == $k){
                            $exattr[$key]['value'] = $v;
                        }
                    }
                }
            }
        }
        //dd($exattr);
        $sign['exattr'] = $exattr;
        $html = response()->view('admin.article.ajax_exattr',$sign)->getContent();
        return response()->json(['status'=>1,'html'=>$html]);
    }

}
