@extends("admin.include.mother")

@section("content")
    @component('admin.component.breadcrumb',['is_back'=>true])
        @slot('name')
            编辑导航
        @endslot
    @endcomponent
    <div class="h30"></div>


    <form>
        <input type="hidden" name="id" value="{{$menu->id}}" />
        <div class="form-group">
            <label for="name">父级</label>
            <span class="text-muted pl-2 js-pid">{{$parent->name ?? '根菜单'}}</span>
        </div>
        <input name="parent_id" type="hidden" value="">
        <div class="form-group">
            <label for="name">菜单名称</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="菜单名称" style="width:400px;" value="{{$menu->name}}" />
            <small class="form-text text-muted">1-20个字符</small>
        </div>
        <div class="form-group">
            <label for="url">路径</label>
            <input type="text" class="form-control" id="url" name="url" placeholder="路径" style="width:400px;" value="{{$menu->url ?? ''}}">
            <small class="form-text text-muted">路由地址</small>
        </div>
        <div class="form-group">
            <label for="target">打开方式</label>
            <input type="text" class="form-control" id="target" name="target" placeholder="打开方式" style="width:400px;" value="{{$menu->target ?? ''}}">
            <small class="form-text text-muted">当前窗口：_self，新窗口：_blank</small>
        </div>
        <div class="form-group">
            <label>状态</label>
            <div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="is_show1" name="is_show" class="custom-control-input" value="1"
                    @if($menu->is_show == 1)
                        checked
                    @endif
                    >
                    <label class="custom-control-label" for="is_show1">开启</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="is_show2" name="is_show" class="custom-control-input" value="9"
                    @if($menu->is_show == 9)
                        checked
                    @endif
                    >
                    <label class="custom-control-label" for="is_show2">关闭</label>
                </div>
            </div>
        </div>

        <div class="h10"></div>
        <button type="submit" class="btn btn-primary" onclick="return post_edit();">保存</button>
    </form>
    <script>
        //提交编辑
        function post_edit(){
            $.ajax({
                type:'post',
                url:'/admin/menu/edit',
                data:$('form').serialize(),
                success:function(res){
                    if(res.status == 0){
                        $boot.warn({text:res.msg},function(){
                            $('input[name='+res.field+']').focus();
                        });
                    }else{
                        $boot.success({text:res.msg},function(){
                            window.location = "{{ url()->previous() }}";
                        });
                    }
                }
            })
            return false;
        }


    </script>

@endsection