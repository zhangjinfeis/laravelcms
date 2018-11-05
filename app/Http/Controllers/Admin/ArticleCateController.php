<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use App\Models\ArticleCate;
use App\Models\Article;
use App\Models\ArticleExattr;
use App\Library\LArticle;
use Illuminate\Support\Facades\DB;
/**
 * 后台分类控制器
 * @author my 2017-10-25
 * Class MenuController
 * @package App\Http\Controllers\Admin
 */
class ArticleCateController extends Controller
{

    /**
     * 分类列表
     * @author my  2017-10-25
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        $list = ArticleCate::getList();
        $ids = [];
        foreach($list as $key => $val){
            array_push($ids,$val['id']);
            $list[$key]['exattr_keys'] = [];
        }
        //dd($list);
        $attrs = ArticleExattr::whereIn('cate_id',$ids)->select('id','cate_id','key')->orderBy('sort','asc')->orderBy('id','asc')->get()->toArray();
        //dd($attrs);
        foreach($list as $ke => $va){
            foreach($attrs as $k => $v){
                if($va['id'] == $v['cate_id']){
                    $list[$ke]['exattr_keys'][] = $v['key'];
                }
            }
        }

        $sign['list'] = $list;
        return view('admin/article_cate/index', $sign);
    }

    /**
     * 创建分类
     * @author my  2017-10-25
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function ajaxCreate(Request $request){
        $rule = [
            'name_cn'=>'required|between:1,20',
        ];
        //如果有英文名称
        if($request->name_en){
            $rule['name_en'] = 'required|between:1,20';
        }

        $message = [
            'required' => ':attribute不能为空',
            'name_cn.between' => ':attribute字符长度1-20',
            'name_en.between' => ':attribute字符长度1-20',
        ];
        $replace = [
            'name_cn'=>'分类名称',
            'name_en'=>'英文名称',
        ];

        $validator = Validator::make($request->all(),$rule,$message,$replace);
        if ($validator->fails()){
            return response()->json(['status'=>0,'msg'=>$validator->errors()->first()]);
        }

        $data['name_cn'] = $request->name_cn;
        $data['name_en'] = $request->name_en;
        $data['is_show'] = $request->is_show;

        $manager_menu = ArticleCate::create($data);  //默认创建的都是根节点，创建根节点

        if($request->parent_id){   //如果不是根节点，第二步：移动到对应位置
            $root = ArticleCate::find($request->parent_id);
            $manager_menu->makeChildOf($root);
            ArticleCate::where('id',$request->parent_id)->update(['is_able'=>9]);
        }
        return response()->json(['status'=>1,'msg'=>'添加成功']);
    }

    /**
     * 删除分类
     * @author my  2017-10-25
     * @param Request $request 请求
     * @return array
     */
    public function ajaxDel(Request $request){
        foreach($request->ids as $vo){
            $menu = ArticleCate::find($vo);
            if($menu){
                $menu->delete();
            }
        }
        return ['status'=>1,'msg'=>'删除成功'];
    }

    /**
     * 开启
     * @author zjf ${date}
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxIsShow(Request $request){
        $res = ArticleCate::whereIn('id',$request->ids)->update(['is_show'=>1]);
        if($res){
            //重置is_show
            LArticle::cate_resetIsShow();
            return response()->json(['status'=>1,'msg'=>'开启成功']);
        }else{
            return response()->json(['status'=>0,'msg'=>'开启失败']);
        };
    }

    /**
     * 关闭
     * @author zjf ${date}
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxUnShow(Request $request){
        $res = ArticleCate::whereIn('id',$request->ids)->update(['is_show'=>9]);
        if($res){
            //重置is_show
            LArticle::cate_resetIsShow();
            return response()->json(['status'=>1,'msg'=>'关闭成功']);
        }else{
            return response()->json(['status'=>0,'msg'=>'关闭失败']);
        };
    }

    /**
     * 跳转到编辑分类页面
     * @author my  2017-10-25
     * @param Request $request
     * @param ArticleCate $menu 依赖注入的分类模型
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request){
        if($request->isMethod('post')){
            $rule = [
                'name_cn'=>'required|between:1,20',
            ];
            //如果有英文名称
            if($request->name_en){
                $rule['name_en'] = 'required|between:1,20';
            }

            $message = [
                'required' => ':attribute不能为空',
                'name_cn.between' => ':attribute字符长度1-20',
                'name_en.between' => ':attribute字符长度1-20',
            ];
            $replace = [
                'name_cn'=>'分类名称',
                'name_en'=>'英文名称',
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

            $menu = ArticleCate::find($request->id);
            $menu->name_cn = $request->name_cn;
            $menu->name_en = $request->name_en;
            $menu->is_show = $request->is_show;
            if($menu->save()){
                return response()->json(['status'=>1,'msg'=>'编辑成功']);
            }else{
                return response()->json(['status'=>0,'msg'=>'编辑失败']);
            }

        }else{
            $sign['menu'] = ArticleCate::find($request->id);
            $sign['parent'] = ArticleCate::find($sign['menu']->parent_id);

            //载入附加字段
            $exattr = ArticleExattr::where('cate_id',$request->id)->orderBy('sort','asc')->orderBy('id','asc')->get();
            $sign['exattr'] = $exattr;
            return view('admin/article_cate/edit',$sign);
        }
    }

    /**
     * 移动分类到某个分类下
     * @author zjf ${date}
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxMove(Request $request){
        $menu = ArticleCate::find($request->move_id);
        $to_menu = ArticleCate::find($request->move_to_id);
        switch ($request->move_method){
            case 'child' :
                $res = $menu->makeChildOf($to_menu);
                break;
            case 'before':
                $res = $menu->moveToLeftOf($to_menu);
                break;
            case 'after':
                $res = $menu->moveToRightOf($to_menu);
                break;
        }
        LArticle::cate_resetIsAble();
        if($res){
            return response()->json(['status'=>1,'msg'=>'移动成功']);
        }else{
            return response()->json(['status'=>0,'msg'=>'移动失败']);
        };
    }


    /**
     * 移动内容到某个分类下
     * @author zjf ${date}
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxMoveContent(Request $request){
        $res = Article::where('cate_id',$request->move_id)->update(['cate_id'=>$request->move_to_id]);
        if($res){
            return response()->json(['status'=>1,'msg'=>'移动成功']);
        }else{
            return response()->json(['status'=>0,'msg'=>'移动失败']);
        };
    }

    /**
     * 复制附加字段
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Created by zjf
     * Time: 2018/11/5 14:49
     */
    public function ajaxCopyExattr(Request $request){

        session(['article_exattr_copy_id'=>$request->id]);
        return response()->json(['status'=>1,'msg'=>'复制成功']);
    }

    /**
     * 粘贴附加字段
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Created by zjf
     * Time: 2018/11/5 14:50
     */
    public function ajaxPasteExattr(Request $request){
        $id = session('article_exattr_copy_id');
        if(!$id){
            return response()->json(['status'=>0,'msg'=>'请先复制一个内容']);
        }
        $attr = ArticleExattr::where('cate_id',$id)->orderBy('sort','asc')->orderBy('id','asc')->get()->toArray();
        foreach($attr as $key=>$val){
            unset($attr[$key]['id']);
            unset($attr[$key]['created_at']);
            unset($attr[$key]['updated_at']);
            $attr[$key]['cate_id'] = $request->id;
        }
        ArticleExattr::where('cate_id',$request->id)->delete();
        $res = DB::table('article_exattr')->insert($attr);
        return response()->json(['status'=>1,'msg'=>'粘贴成功']);
    }

}