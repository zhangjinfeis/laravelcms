@extends("admin.include.mother")

@section("content")
    <div class="clearfix">
        <div class="float-left">
            @component('admin.component.breadcrumb')
                @slot('name')
                    图片
                @endslot
            @endcomponent
        </div>
        <div class="float-right">
            <button type="button" class="btn btn-sm btn-primary" onclick="return ajax_clear();"><i class="fa fa-eraser"></i> 清除过期图片</button>
        </div>
    </div>
    <div class="h15"></div>



    <div class="m-pic clearfix">
        @foreach($list as $vo)
            <div class="item">
                <div class="pic" onclick="return picDetail('{{$vo->md5}}');">
                    <img src="{{$vo->path}}" />
                </div>
                <div class="name" title="{{$vo->name}}">{{$vo->substr_name}}</div>
                <div class="px">{{$vo->width}}*{{$vo->height}}px</div>
                <div class="size">{{format_bytes($vo->size)}}</div>
            </div>
        @endforeach
    </div>

    <div class="pagination-warp mt10">
        {{$list->links()}}
    </div>

    @component('admin.component.picDetail') @endcomponent


    <script>

        //清除垃圾图片
        function ajax_clear(){
            $boot.confirm({text:'即将清除过期图片，是否继续？'},function () {
                var loading = $boot.loading({text:"清除中..."});
                $.ajax({
                    type:'post',
                    url:"{{url('/admin/pic/ajax_clear')}}",
                    success:function(res){
                        loading.close();
                        if(res.status == 1){
                            $boot.success({text:res.msg},function(){
                                window.location.reload();
                            });
                        }else{
                            $boot.error({text:res.msg});
                        }
                    }
                });
            });
            return false;
        }
    </script>


@endsection