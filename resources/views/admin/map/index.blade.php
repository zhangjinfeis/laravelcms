@extends("admin.include.mother")

@section("content")

    <div class="clearfix">
        <div class="float-left">
            @component('admin.component.breadcrumb')
                @slot('name')
                    地图列表
                @endslot
            @endcomponent
        </div>
        <div class="float-right">
            <a role="button" class="btn btn-sm btn-primary" href="{{url('/admin/map/create_edit')}}"><i class="fa fa-plus"></i> 新增地图</a>
        </div>
    </div>
    <div class="h15"></div>

    <table class="table table-sm table-hover table-bb">

            <tr>
                <th width="40">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checkbox-all" id="checkbox-0">
                        <label class="custom-control-label"  for="checkbox-0">&nbsp;</label>
                    </div>
                </th>
                <th>ID</th>
                <th>地图标题</th>
                <th>经/纬度</th>
                <th>地址</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>

        @foreach($list as $vo)
            <tr>
                <td>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checkbox-item" id="checkbox-{{$vo->id}}" data-id="{{$vo->id}}">
                        <label class="custom-control-label"  for="checkbox-{{$vo->id}}">&nbsp;</label>
                    </div>
                </td>
                <td>{{$vo->id}}</td>
                <td>
                    {{$vo->title}}

                </td>
                <td>
                    {{$vo->lng}}/{{$vo->lat}}
                </td>
                <td>
                    {{$vo->address}}
                </td>
                <td>
                    {{$vo->created_at->format('Y-m-d H:i')}}
                </td>
                <td>
                    <a class="btn btn-sm btn-light" href="{{url('/admin/map/create_edit?id='.$vo['id'])}}" role="button" title="编辑"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-sm btn-light" href="#" role="button" onclick="return del_one({{$vo['id']}});" title="删除"><i class="fa fa-trash"></i></a>

                </td>
            </tr>
        @endforeach

    </table>
    <div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="return ajax_del_all();">删除</button>
    </div>
    <div class="pagination-warp mt10">
        {{$list->links()}}
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
                    url:'/admin/map/ajax_del',
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

        //删除一篇文章
        function del_one(id){
            $boot.confirm({text:'确认删除当前文章？'},function(){
                if(!id){
                    $boot.warn({text:'删除参数出错'});
                    return false;
                }
                $.ajax({
                    type:'post',
                    url:'/admin/map/ajax_del',
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
@endsection