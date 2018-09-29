@extends("admin.include.mother")

@section("content")
    @component('admin.component.breadcrumb',['is_back'=>true])
        @slot('name')
            编辑参数分类
        @endslot
    @endcomponent
    <div class="h30"></div>


    <form>
        <input type="hidden" name="id" value="{{$cate->id}}" />
        <input name="parent_id" type="hidden" value="">
        <div class="form-group">
            <label for="name"><span class="text-danger">* </span>分类名称</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="分类名称" style="width:400px;" value="{{$cate->name}}" />
            <small class="form-text text-muted">1-20个字符</small>
        </div>
        <div class="form-group">
            <label for="sort">排序</label>
            <input type="text" class="form-control" id="sort" name="sort" placeholder="排序" style="width:400px;" value="{{$cate->sort}}" />
            <small class="form-text text-muted">数值越小排名越靠前</small>
        </div>
        <div class="h10"></div>
        <button type="submit" class="btn btn-primary" onclick="return post_edit();">保存</button>
    </form>
    <script>
        //提交编辑
        function post_edit(){
            $.ajax({
                type:'post',
                url:'/admin/config_cate/edit',
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