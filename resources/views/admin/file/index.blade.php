@extends("admin.include.mother")

@section("content")
    <div class="clearfix">
        <div class="float-left">
            @component('admin.component.breadcrumb')
                @slot('name')
                    文件
                @endslot
            @endcomponent
        </div>
        <div class="float-right">
            <button type="button" class="btn btn-sm btn-primary" onclick="return ajax_clear();"><i class="fa fa-eraser"></i> 清除过期文件</button>
        </div>
    </div>
    <div class="h15"></div>

    <div class="m-pic-note clearfix">
        <div class="item">
            <span class="is_used is_used_on"></span>&nbsp;使用中
        </div>
        <div class="item">
            <span class="is_used"></span>&nbsp;未使用
        </div>
    </div>
    <div class="h15"></div>


    <div class="m-file clearfix">
        @foreach($list as $vo)
            <div class="item clearfix" onclick="return fileDetail('{{$vo->md5}}')">
                <span class="is_used {{$vo->is_used==1?'is_used_on':''}}"></span>
                <div class="ext">{{$vo->ext}}</div>
                <div class="right">
                    <div class="name" title="{{$vo->original_name}}">{{$vo->original_name}}</div>
                    <div class="size">{{format_bytes($vo->size)}}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="pagination-warp mt10">
        {{$list->links()}}
    </div>

    @component('admin.component.fileDetail') @endcomponent


    <script>

        //清除垃圾图片
        function ajax_clear(){
            $boot.confirm({text:'即将清除过期文件，是否继续？'},function () {
                var loading = $boot.loading({text:"清除中..."});
                $.ajax({
                    type:'post',
                    url:"{{url('/admin/file/ajax_clear')}}",
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