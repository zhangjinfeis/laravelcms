@extends("admin.include.mother")

@section("content")
    <div class="clearfix">
        <div class="float-left">
            @component('admin.component.breadcrumb')
                @slot('name')
                    文章列表
                @endslot
            @endcomponent
        </div>
        <div class="float-right">
            <a role="button" class="btn btn-sm btn-primary" href="{{url('/admin/article/create?cate_id='.($_GET['cate_id']??''))}}"><i class="fa fa-plus"></i> 新增文章</a>
        </div>
    </div>

    <div class="h15"></div>


    <div class="clearfix">
        <div class="float-left">
            <form class="form-inline" action="{{url('/admin/article')}}">
                <select class="form-control form-control-sm" name="cate_id">
                    <option value="">选择分类</option>
                    @foreach($cate as $vo)
                        <option value="{{$vo['id']}}" @if($vo['id'] == request('cate_id')) selected @endif>{!!$vo['depth_name']!!}{{$vo['name_cn']}}({{$vo['count']}})</option>
                    @endforeach
                </select>
                <input class="form-control form-control-sm ml-1" type="text" name="title" placeholder="关键字搜索" value="{{request('title')}}">&nbsp;
                 状态&nbsp;<select class="form-control form-control-sm" name="is_show">
                    <option value="">全部</option>
                    <option value="1" {{request('is_show') == 1?'selected':''}}>开启</option>
                    <option value="9" {{request('is_show') == 9?'selected':''}}>关闭</option>
                </select>
                <button class="btn btn-sm btn-primary ml-1">搜索</button>
            </form>
        </div>
        <div class="float-right">
            <span class="mt-1 d-block text-muted">共找到{{$count}}条记录</span>
        </div>
    </div>

    <div class="h15"></div>

    <table class="table table-sm table-hover" style="border-bottom:#eee 1px solid;">
        <tr>
            <th width="40">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checkbox-all" id="checkbox-0">
                    <label class="custom-control-label"  for="checkbox-0">&nbsp;</label>
                </div>
            </th>
            <th>ID</th>
            <th>标题</th>
            <th>文章分类</th>
            <th>排序</th>
            <th>开启/关闭</th>
            <th>最近更新</th>
            <th>操作</th>
        </tr>
    @foreach($list as $vo)
        <tr>
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checkbox-item" id="checkbox-{{$vo->id}}" data-id="{{$vo->id}}">
                    <label class="custom-control-label" for="checkbox-{{$vo->id}}">&nbsp;</label>
                </div>
            </td>
            <td>{{$vo['id']}}</td>
            <td>
                {{$vo->title}}
                @if($vo->thumb)
                    &nbsp;<span class="text-warning"><i class="fa fa-picture-o"></i></span>
                @endif
            </td>
            <td>
                {{$vo->cate->name_cn ?? ''}}
            </td>
            <td>
                {{$vo->sort}}
            </td>
            <td>
                @if($vo['is_show'] ==1)
                    <span class="badge badge-success">开启</span>
                @else
                    <span class="badge badge-danger">关闭</span>
                @endif
            </td>
            <td>
                {{$vo->updated_at->format('Y-m-d H:i')}}
            </td>
            <td>
                <a class="btn btn-sm btn-light" href="{{url('/admin/article/edit?id='.$vo['id'].'&jump='.encrypt($_GET))}}" role="button" title="编辑"><i class="fa fa-edit"></i></a>
                <a class="btn btn-sm btn-light" href="#" role="button" onclick="return del_one({{$vo['id']}});" title="删除"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        @endforeach
    </table>
    <div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="return ajax_del_all();">删除</button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="return alert_move_win();">移动到</button>
    </div>

    <div class="pagination-warp mt10">
        {{$list->links()}}
    </div>


    <div id="win1" class="hide">
        <div>
            <div class="form-group">
                <label class="js-move-tip">移动到以下菜单下</label>
                <select class="form-control" name="move_to_id">
                    @foreach($cate as $vo)
                        <option @if($vo['is_able'] == 9) disabled @endif value="{{$vo['id']}}">{!!$vo['depth_name']!!}{{$vo['name_cn']}}({{$vo['count']}})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="h10"></div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary" onclick="return ajax_move_all();">移动</button>
            </div>
        </div>
    </div>

    <script>

        //删除文章all
        function ajax_del_all(){
            var ids = [];
            $('.checkbox-item').filter(':checked').each(function(){
                ids.push($(this).attr('data-id'));
            });
            if(ids.length < 1){
                $boot.error({text:'请至少选择一个选项'});
                return false;
            }
            $boot.confirm({text:'确认删除所选？'},function(){
                $.ajax({
                    type:'post',
                    url:'/admin/article/ajax_del',
                    data:{ids:ids},
                    success:function(res){
                        if(res.status == 0){
                            $boot.error({text:res.msg});
                        }else{
                            $boot.success({text:res.msg},function(){
                                window.location = window.location;
                            });

                        }
                    }
                });
            });
            return false;
        }



        //移动文章all-弹出移动分类窗口
        function alert_move_win(){
            var ids = [];
            $('.checkbox-item').filter(':checked').each(function(){
                ids.push($(this).attr('data-id'));
            });
            if(ids.length < 1){
                $boot.error({text:'请至少选择一个选项'});
                return false;
            }
            $boot.win({id:'#win1','title':'移动分类'});
            return false;
        }
        //移动文章all
        function ajax_move_all(){
            var ids = [];
            $('.checkbox-item').filter(':checked').each(function(){
                ids.push($(this).attr('data-id'));
            });
            var move_to_id = $('select[name=move_to_id]').val();
            $.ajax({
                type:'post',
                url:'/admin/article/ajax_move',
                data:{ids:ids,move_to_id:move_to_id},
                success:function(res){
                    if(res.status == 0){
                        $boot.error({text:res.msg});
                    }else{
                        $boot.success({text:res.msg},function(){
                            window.location = window.location;
                        });

                    }
                }
            });
            return false;
        }

        //删除一篇文章
        function del_one(id){
            $boot.confirm({text:'确认删除当前文章？'},function(){
                if(!id){
                    $boot.warn({text:'删除参数出错'});
                    return false;
                }
                $.ajax({
                    type:'post',
                    url:'/admin/article/ajax_del',
                    data:{ids:[id]},
                    success:function(res){
                        if(res.status == 0){
                            $boot.warn({text:res.msg});
                        }else{
                            $boot.success({text:res.msg},function(){
                                window.location = window.location;
                            });

                        }
                    }
                });
            });
            return false;
        }
    </script>

@stop
