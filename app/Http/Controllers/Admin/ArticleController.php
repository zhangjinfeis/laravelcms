<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use App\Models\ArticleCate;
use App\Models\Article;
use App\Models\ArticleExattr;
use App\Models\Pic;

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
              //标题搜索
        $query = Article::query();
        if($request->title&&$request->title!=''){
            $query=$query->where('title','like','%'.$request->title.'%');
        }
        //开启状态搜索
        if($request->is_show=='on') {
            $query=$query->where(['is_show'=>1]);
        }elseif($request->is_show=='off'){
            $query=$query->where(['is_show'=>9]);
        }else{
            $query=$query->whereIn('is_show',[9,1]);
        }
        //分类搜索
        if($request->cate_id&&$request->cate_id!=''){
            $w['parent_id'] = $request->cate_id;
            $s=ArticleCate::where($w)->get();
            if(count($s)>0){
                foreach($s as $v){
                    $wherea[] = $v['id'];
                }
                $query=$query->whereIn('cate_id', $wherea);
            } else{
                $query=$query->where('cate_id', $request->cate_id);
            }
        }else{
            $allcate=ArticleCate::where(['is_show'=>1])->pluck('id')->toArray();
            $query=$query->whereIn('cate_id', $allcate);
        }

        $list= $query->orderBy('sort','asc')
            ->orderBy('updated_at','desc')
            ->paginate(15);
        $sign['list']=$list;

        foreach($sign['list'] as $k=>$v){
            $c= json_decode($v['exattr'],true);
            if(!is_null($c)&&count($c)>0){
                foreach($c as $kk=>$vv){
                    $v[$kk]=$vv;
                }
            }
        }
        //文章分类
        $cate = ArticleCate::getList();
        //dd($cate);
        $sign['cate'] = $cate;
        //总记录数
        $sign['count'] = $query->count();
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
            $article['title'] = $request->title;
            $article['title_sub'] = $request->title_sub;
            $article['cate_id'] = $request->cate_id;
            $article['url'] = $request->url;
            $article['thumb'] = $request->thumb;
            $article['body'] = htmlspecialchars($request->body);
            $article['keywords'] = $request->keywords;
            $article['description'] = $request->description;
            $article['is_show'] = $request->is_show;
            $article['exattr'] = json_encode($request->exattr);
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
            //dd($request->body);

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
        $sign['exattr'] = $exattr;
        $html = response()->view('admin.article.ajax_exattr',$sign)->getContent();
        return response()->json(['status'=>1,'html'=>$html]);
    }

}
