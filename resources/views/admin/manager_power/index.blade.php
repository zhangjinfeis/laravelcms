@extends("admin.include.mother")

@section("content")
    <div class="clearfix">
        <div class="float-left">
            @component('admin.component.breadcrumb')
                @slot('name')
                    权限
                @endslot
            @endcomponent
        </div>
        <div class="float-right">
            <a role="button" class="btn btn-sm btn-primary" href="#" onclick="return alert_win();"><i class="fa fa-plus"></i> 新增权限</a>
        </div>
    </div>
    <div class="h15"></div>



    <div id="as" class="hide">
        <div class="">
            <div class="form-group">
                <label for="name"><span class="text-danger">* </span>权限名称</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="权限名称">
                <small class="form-text text-muted">如：create_user，指定权限规则</small>
            </div>
            <div class="form-group">
                <label for="url"><span class="text-danger">* </span>描述</label>
                <input type="text" class="form-control" id="description" name="description" placeholder="描述">
            </div>
            <div class="form-group">
                <label for="url">分组标签</label>
                <input type="text" class="form-control" id="group" name="group" placeholder="标签分组" value="{{request('group')}}">
            </div>
            <div class="h10"></div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary" onclick="return create_power();">新增</button>
            </div>
        </div>
    </div>

    <script>
        //弹出创建菜单窗口
        function alert_win(){
            $boot.win({id:'#as',title:'新增权限'});
            return false;
        }
        //新增权限提交
        function create_power(){
            if(!$('input[name=name]').val()){
                $boot.warn({text:'权限名称不能为空'});
                return false;
            }
            var data = {
                name:$('input[name=name]').val(),
                description:$('input[name=description]').val(),
                group:$('input[name=group]').val(),
            };
            $.ajax({
                type:'post',
                url:'/admin/manager_power/ajax_create',
                data:data,
                success:function(res){
                    if(res.status == 0){
                        $boot.warn({text:res.msg});
                    }else{
                        window.location = window.location;
                    }
                }
            });
        }
    </script>

    <div class="h15"></div>
    <div>
        分组标签：
        @foreach($groups as $vo)
            @if(isset($_GET['group']) && $vo->group == $_GET['group'])
                <a role="button" class="btn btn-sm btn-success btn-rounded js-group" href="{{url('/admin/manager_power').'?group='.$vo->group}}">{{$vo->group}}</a>
            @else
                <a role="button" class="btn btn-sm btn-outline-success btn-rounded js-group" href="{{url('/admin/manager_power').'?group='.$vo->group}}">{{$vo->group}}</a>
            @endif

        @endforeach
    </div>

    <div class="h15"></div>
    <table class="table table-sm table-hover table-bb">
        <tr>
            <th data-field="id">ID</th>
            <th data-field="name">权限名称</th>
            <th data-field="price">描述</th>
            <th data-field="price">分组标签</th>
            <th data-field="price">操作</th>
        </tr>

        @foreach($list as $vo)
            <tr>
                <td>{{$vo['id']}}</td>
                <td>{{$vo['name']}}</td>
                <td>{{$vo['description']}}</td>
                <td>{{$vo['group']}}</td>
                <td>
                    <a class="btn btn-sm btn-light" href="{{url('/admin/manager_power/edit?id='.$vo['id'])}}" role="button" title="编辑"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-sm btn-light" href="#" role="button" onclick="return del_power({{$vo['id']}});" title="删除"><i class="fa fa-trash"></i></a>
                </td>

            </tr>
        @endforeach

    </table>
    <div class="pagination-warp mt10">
        {{$list->links()}}
    </div>


    <script>
        function del_power(id){
            $boot.confirm({text:'确认删除该菜单？'},function(){
                if(!id){
                    $boot.warn({text:'删除参数出错'});
                    return false;
                }
                $.ajax({
                    type:'post',
                    url:'/admin/manager_power/ajax_del',
                    data:{id:id},
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